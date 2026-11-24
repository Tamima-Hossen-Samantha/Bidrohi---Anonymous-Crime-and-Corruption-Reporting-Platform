<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\Report;
use App\Models\Ranger;
use Illuminate\Support\Facades\DB;

class ZoneScoreService
{
    /**
     * Fully recalculate a zone's score from scratch.
     * Called after report verification or admin action.
     */
    public function recalculate(Zone $zone): Zone
    {
        $stats = Report::where('zone_id', $zone->id)
            ->selectRaw("
                COUNT(*)                                        as total,
                SUM(status = 'verified')                        as verified,
                SUM(status = 'escalated')                       as escalated,
                SUM(severity = 'critical' AND status = 'verified') as critical_count,
                SUM(severity = 'high'     AND status = 'verified') as high_count,
                SUM(severity = 'medium'   AND status = 'verified') as medium_count,
                SUM(category = 'child_abuse' AND status = 'verified') as child_abuse_count
            ")
            ->first();

        $score = $this->computeScore($stats);

        $zone->update([
            'score'          => $score,
            'report_count'   => $stats->total,
            'verified_count' => $stats->verified,
            'status'         => $this->statusFromScore($score),
        ]);

        // Update controlling ranger
        $this->updateControllingRanger($zone);

        return $zone->fresh();
    }

    /**
     * Bulk recalculate all zones. Run this on a schedule.
     */
    public function recalculateAll(): void
    {
        Zone::all()->each(fn($zone) => $this->recalculate($zone));
    }

    /**
     * Score formula:
     * - Verified reports:         +15 each (capped at 40 pts)
     * - High severity reports:    +10 each (capped at 25 pts)
     * - Critical severity:        +20 each (capped at 30 pts)
     * - Child abuse verified:     +5  each (capped at 20 pts)
     * - Escalated reports:        +8  each (capped at 20 pts)
     * Total max: 100
     */
    private function computeScore(object $stats): int
    {
        $score = 0;

        $score += min(40, $stats->verified      * 15);
        $score += min(25, $stats->high_count    * 10);
        $score += min(30, $stats->critical_count * 20);
        $score += min(20, $stats->child_abuse_count * 5);
        $score += min(20, $stats->escalated     * 8);

        return min(100, (int) $score);
    }

    private function statusFromScore(int $score): string
    {
        if ($score >= 70) return 'red';
        if ($score >= 40) return 'yellow';
        return 'green';
    }

    /**
     * The ranger with the most verified reports in this zone controls it.
     */
    private function updateControllingRanger(Zone $zone): void
    {
        $topRanger = Report::where('zone_id', $zone->id)
            ->where('status', 'verified')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->first();

        if ($topRanger) {
            $ranger = \App\Models\User::find($topRanger->user_id)?->ranger;
            if ($ranger) {
                // Remove old controller bonus if different ranger
                $oldControllerId = $zone->controlling_ranger_id;
                if ($oldControllerId && $oldControllerId !== $ranger->id) {
                    Ranger::where('id', $oldControllerId)
                          ->decrement('zones_controlled');
                }

                $zone->update(['controlling_ranger_id' => $ranger->id]);

                // Award zone capture if newly captured
                if ($oldControllerId !== $ranger->id) {
                    $ranger->increment('zones_controlled');
                    \App\Models\RangerAction::log($ranger, 'zone_captured', null, [
                        'zone_id'   => $zone->id,
                        'zone_name' => $zone->name,
                    ]);
                }
            }
        }
    }
}