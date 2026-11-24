<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ranger;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * GET /api/leaderboard
     * Global top rangers — public.
     */
    public function global(): JsonResponse
    {
        $rangers = Ranger::with('user')
            ->orderByDesc('points')
            ->limit(50)
            ->get()
            ->map(fn($ranger, $index) => [
                'position'          => $index + 1,
                'username'          => $ranger->user->username,
                'district'          => $ranger->district,
                'points'            => $ranger->points,
                'rank'              => $ranger->rank,
                'rank_en'           => $ranger->rank_en,
                'reports_submitted' => $ranger->reports_submitted,
                'reports_verified'  => $ranger->reports_verified,
                'zones_controlled'  => $ranger->zones_controlled,
                'is_verified'       => $ranger->is_verified,
                'last_active_at'    => $ranger->last_active_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $rangers,
        ]);
    }

    /**
     * GET /api/leaderboard/district/{district}
     * Top rangers in a specific district — public.
     */
    public function byDistrict(string $district): JsonResponse
    {
        $rangers = Ranger::with('user')
            ->where('district', $district)
            ->orderByDesc('points')
            ->limit(20)
            ->get()
            ->map(fn($ranger, $index) => [
                'position'          => $index + 1,
                'username'          => $ranger->user->username,
                'district'          => $ranger->district,
                'points'            => $ranger->points,
                'rank'              => $ranger->rank,
                'rank_en'           => $ranger->rank_en,
                'reports_submitted' => $ranger->reports_submitted,
                'reports_verified'  => $ranger->reports_verified,
                'zones_controlled'  => $ranger->zones_controlled,
            ]);

        return response()->json([
            'success'  => true,
            'district' => $district,
            'data'     => $rangers,
        ]);
    }

    /**
     * GET /api/leaderboard/zones
     * Which ranger controls which zone — for the map.
     */
    public function zoneControl(): JsonResponse
    {
        $zones = Zone::with('controllingRanger.user')
            ->whereNotNull('controlling_ranger_id')
            ->orderByDesc('score')
            ->get()
            ->map(fn($zone) => [
                'zone_id'    => $zone->id,
                'zone_name'  => $zone->name,
                'zone_bn'    => $zone->name_bn,
                'status'     => $zone->status,
                'score'      => $zone->score,
                'latitude'   => $zone->latitude,
                'longitude'  => $zone->longitude,
                'controller' => $zone->controllingRanger ? [
                    'username'        => $zone->controllingRanger->user->username,
                    'rank'            => $zone->controllingRanger->rank,
                    'rank_en'         => $zone->controllingRanger->rank_en,
                    'points'          => $zone->controllingRanger->points,
                ] : null,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $zones,
        ]);
    }

    /**
     * GET /api/leaderboard/ranks
     * Count of rangers at each rank — for public dashboard stats.
     */
    public function rankDistribution(): JsonResponse
    {
        $distribution = Ranger::select('rank', 'rank_en', DB::raw('COUNT(*) as count'))
            ->groupBy('rank', 'rank_en')
            ->orderByDesc('count')
            ->get();

        // Total rangers
        $total = Ranger::count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total'        => $total,
                'distribution' => $distribution,
                'ranks'        => Ranger::RANKS,
            ],
        ]);
    }
}