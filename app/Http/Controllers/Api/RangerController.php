<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ranger;
use App\Models\RangerAction;
use App\Models\User;
use App\Services\ZoneScoreService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RangerController extends Controller
{
    /**
     * GET /api/rangers/{username}
     * Public ranger profile — anyone can view.
     */
    public function profile(string $username): JsonResponse
    {
        $user = User::where('username', $username)
                    ->where('role', 'ranger')
                    ->firstOrFail();

        $ranger = $user->ranger;

        // Recent actions (last 10)
        $recentActions = RangerAction::where('ranger_id', $ranger->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['action_type', 'points_earned', 'description', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => [
                'username'            => $user->username,
                'district'            => $ranger->district,
                'points'              => $ranger->points,
                'rank'                => $ranger->rank,
                'rank_en'             => $ranger->rank_en,
                'reports_submitted'   => $ranger->reports_submitted,
                'reports_verified'    => $ranger->reports_verified,
                'zones_controlled'    => $ranger->zones_controlled,
                'is_verified'         => $ranger->is_verified,
                'badge_data'          => $ranger->badge_data ?? [],
                'points_to_next_rank' => $ranger->pointsToNextRank(),
                'progress_percent'    => $this->progressPercent($ranger),
                'recent_actions'      => $recentActions,
                'member_since'        => $ranger->created_at->toDateString(),
                'last_active_at'      => $ranger->last_active_at,
            ],
        ]);
    }

    /**
     * GET /api/rangers/me/actions
     * Logged-in ranger's full action history — paginated.
     */
    public function myActions(Request $request): JsonResponse
    {
        $ranger = $request->user()->ranger;

        $actions = RangerAction::where('ranger_id', $ranger->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $actions,
        ]);
    }

    /**
     * GET /api/rangers/me/stats
     * Detailed stats for logged-in ranger.
     */
    public function myStats(Request $request): JsonResponse
    {
        $ranger = $request->user()->ranger;

        // Points breakdown by action type
        $breakdown = RangerAction::where('ranger_id', $ranger->id)
            ->selectRaw('action_type, SUM(points_earned) as total_points, COUNT(*) as count')
            ->groupBy('action_type')
            ->get();

        // Points earned this week
        $weeklyPoints = RangerAction::where('ranger_id', $ranger->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('points_earned');

        // Points earned this month
        $monthlyPoints = RangerAction::where('ranger_id', $ranger->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('points_earned');

        // Global rank position
        $position = Ranger::where('points', '>', $ranger->points)->count() + 1;

        return response()->json([
            'success' => true,
            'data'    => [
                'points'              => $ranger->points,
                'rank'                => $ranger->rank,
                'rank_en'             => $ranger->rank_en,
                'global_position'     => $position,
                'points_to_next_rank' => $ranger->pointsToNextRank(),
                'progress_percent'    => $this->progressPercent($ranger),
                'weekly_points'       => $weeklyPoints,
                'monthly_points'      => $monthlyPoints,
                'reports_submitted'   => $ranger->reports_submitted,
                'reports_verified'    => $ranger->reports_verified,
                'zones_controlled'    => $ranger->zones_controlled,
                'points_breakdown'    => $breakdown,
                'badges'              => $ranger->badge_data ?? [],
                'next_rank'           => $this->nextRankInfo($ranger),
            ],
        ]);
    }

    /**
     * POST /api/rangers/me/district
     * Update home district.
     */
    public function updateDistrict(Request $request): JsonResponse
    {
        $request->validate([
            'district' => 'required|string|max:100',
        ]);

        $ranger = $request->user()->ranger;
        $ranger->update(['district' => $request->district]);

        return response()->json([
            'success' => true,
            'message' => 'District updated.',
            'data'    => ['district' => $ranger->district],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function progressPercent(Ranger $ranger): int
    {
        $ranks = Ranger::RANKS;

        foreach ($ranks as $i => $rank) {
            if ($ranger->points < $rank['min']) {
                $prev = $ranks[$i - 1]['min'] ?? 0;
                $range = $rank['min'] - $prev;
                $earned = $ranger->points - $prev;
                return (int) min(100, ($earned / $range) * 100);
            }
        }

        return 100; // Max rank
    }

    private function nextRankInfo(Ranger $ranger): ?array
    {
        foreach (Ranger::RANKS as $rank) {
            if ($rank['min'] > $ranger->points) {
                return [
                    'name'           => $rank['name'],
                    'name_en'        => $rank['name_en'],
                    'points_needed'  => $rank['min'],
                    'points_to_go'   => $rank['min'] - $ranger->points,
                ];
            }
        }
        return null; // Already বিদ্রোহী
    }
}