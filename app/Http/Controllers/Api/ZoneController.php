<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Hotspot;
use App\Services\ZoneScoreService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ZoneController extends Controller
{
    public function __construct(protected ZoneScoreService $zoneScoreService) {}

    /**
     * GET /api/zones
     * Public — returns all 64 districts with scores for the map.
     */
    public function index(): JsonResponse
    {
        $zones = Zone::with('controllingRanger.user')
            ->orderByDesc('score')
            ->get()
            ->map(fn($zone) => [
                'id'           => $zone->id,
                'name'         => $zone->name,
                'name_bn'      => $zone->name_bn,
                'division'     => $zone->division,
                'status'       => $zone->status,
                'score'        => $zone->score,
                'report_count' => $zone->report_count,
                'latitude'     => $zone->latitude,
                'longitude'    => $zone->longitude,
                'controller'   => $zone->controllingRanger ? [
                    'username' => $zone->controllingRanger->user->username,
                    'rank'     => $zone->controllingRanger->rank,
                ] : null,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $zones,
        ]);
    }

    /**
     * GET /api/zones/{id}
     * Single zone detail with hotspots.
     */
    public function show(int $id): JsonResponse
    {
        $zone = Zone::with(['hotspots' => fn($q) => $q->active(), 'controllingRanger.user'])
                    ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $zone->id,
                'name'           => $zone->name,
                'name_bn'        => $zone->name_bn,
                'division'       => $zone->division,
                'status'         => $zone->status,
                'score'          => $zone->score,
                'report_count'   => $zone->report_count,
                'verified_count' => $zone->verified_count,
                'ranger_count'   => $zone->ranger_count,
                'latitude'       => $zone->latitude,
                'longitude'      => $zone->longitude,
                'hotspots'       => $zone->hotspots,
                'controller'     => $zone->controllingRanger ? [
                    'username'      => $zone->controllingRanger->user->username,
                    'rank'          => $zone->controllingRanger->rank,
                    'rank_en'       => $zone->controllingRanger->rank_en,
                    'points'        => $zone->controllingRanger->points,
                ] : null,
            ],
        ]);
    }

    /**
     * GET /api/zones/heatmap
     * Optimized for map rendering — minimal payload.
     */
    public function heatmap(): JsonResponse
    {
        $zones    = Zone::select(['id','name','name_bn','latitude','longitude','status','score'])->get();
        $hotspots = Hotspot::active()
                           ->select(['id','zone_id','latitude','longitude','intensity','category','radius_meters'])
                           ->get();

        return response()->json([
            'success'  => true,
            'data'     => [
                'zones'    => $zones,
                'hotspots' => $hotspots,
            ],
        ]);
    }
}