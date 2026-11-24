<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Hotspot;
use App\Models\Zone;

class HotspotDetectionService
{
    // Minimum reports in a radius to declare a hotspot
    const MIN_REPORTS   = 3;
    const RADIUS_METERS = 2000; // 2km radius

    /**
     * Detect hotspots for a given zone using simple proximity clustering.
     * This runs locally — Python FastAPI will do advanced k-means clustering.
     * This is the lightweight fallback that runs on every new verified report.
     */
    public function detectForZone(Zone $zone): array
    {
        $reports = Report::where('zone_id', $zone->id)
            ->where('status', 'verified')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'latitude', 'longitude', 'category', 'created_at']);

        if ($reports->count() < self::MIN_REPORTS) {
            return [];
        }

        $clusters = $this->cluster($reports->toArray());
        $hotspots = [];

        foreach ($clusters as $cluster) {
            if (count($cluster['points']) < self::MIN_REPORTS) continue;

            $centroid  = $this->centroid($cluster['points']);
            $intensity = $this->intensity($cluster['points']);
            $category  = $this->dominantCategory($cluster['points']);

            $hotspot = Hotspot::updateOrCreate(
                [
                    'zone_id'   => $zone->id,
                    'latitude'  => round($centroid['lat'], 4),
                    'longitude' => round($centroid['lng'], 4),
                ],
                [
                    'name'              => $zone->name . ' Hotspot',
                    'category'          => $category,
                    'radius_meters'     => self::RADIUS_METERS,
                    'intensity'         => $intensity,
                    'report_count'      => count($cluster['points']),
                    'last_incident_at'  => now(),
                    'is_active'         => true,
                    'ai_generated'      => false,
                ]
            );

            $hotspots[] = $hotspot;
        }

        return $hotspots;
    }

    /**
     * Simple greedy proximity clustering.
     * Groups reports within RADIUS_METERS of each other.
     */
    private function cluster(array $reports): array
    {
        $clusters  = [];
        $assigned  = [];

        foreach ($reports as $i => $report) {
            if (in_array($i, $assigned)) continue;

            $cluster = ['points' => [$report]];
            $assigned[] = $i;

            foreach ($reports as $j => $other) {
                if (in_array($j, $assigned)) continue;

                $distance = $this->haversine(
                    $report['latitude'], $report['longitude'],
                    $other['latitude'],  $other['longitude']
                );

                if ($distance <= self::RADIUS_METERS) {
                    $cluster['points'][] = $other;
                    $assigned[] = $j;
                }
            }

            $clusters[] = $cluster;
        }

        return $clusters;
    }

    /**
     * Haversine formula — distance between two lat/lng points in meters.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function centroid(array $points): array
    {
        $lat = collect($points)->avg('latitude');
        $lng = collect($points)->avg('longitude');
        return ['lat' => $lat, 'lng' => $lng];
    }

    /**
     * Intensity: 0–100 based on report count and recency.
     */
    private function intensity(array $points): int
    {
        $count    = count($points);
        $baseScore = min(60, $count * 10);

        // Recency boost — reports in last 7 days add extra weight
        $recent = collect($points)->filter(function ($p) {
            return isset($p['created_at']) &&
                   now()->diffInDays($p['created_at']) <= 7;
        })->count();

        $recencyBoost = min(40, $recent * 15);

        return min(100, $baseScore + $recencyBoost);
    }

    private function dominantCategory(array $points): string
    {
        $categories = collect($points)->groupBy('category');
        return $categories->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'other';
    }

    /**
     * Deactivate hotspots in zones that have gone quiet (no new reports in 30 days).
     */
    public function deactivateStale(): void
    {
        Hotspot::where('last_incident_at', '<', now()->subDays(30))
               ->where('is_active', true)
               ->update(['is_active' => false]);
    }
}