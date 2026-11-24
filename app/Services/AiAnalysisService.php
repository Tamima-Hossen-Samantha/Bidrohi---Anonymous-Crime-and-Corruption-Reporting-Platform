<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    private string $baseUrl;
    private string $secretKey;

    public function __construct()
    {
        $this->baseUrl   = config('services.ai.url', 'http://127.0.0.1:8001');
        $this->secretKey = config('services.ai.secret', 'bidrohi_ai_secret_2026');
    }

    /**
     * Send a report to the AI microservice for analysis.
     * Called right after report submission.
     */
    public function analyzeReport(Report $report): bool
    {
        try {
            $response = Http::withHeaders([
                'X-Secret-Key' => $this->secretKey,
                'Accept'       => 'application/json',
            ])->post("{$this->baseUrl}/api/analyze", [
                'report' => [
                    'id'          => $report->id,
                    'title'       => $report->title,
                    'description' => $report->description,
                    'category'    => $report->category,
                    'district'    => $report->district,
                    'latitude'    => $report->latitude,
                    'longitude'   => $report->longitude,
                    'created_at'  => $report->created_at->toISOString(),
                ],
            ]);

            if ($response->successful()) {
                $data     = $response->json();
                $analysis = $data['analysis'];

                // Update report with AI results
                $report->update([
                    'severity'     => $analysis['severity'],
                    'ai_tags'      => $analysis['ai_tags'],
                    'ai_processed' => true,
                    'ngo_alerted'  => !empty($data['alerts']),
                ]);

                return true;
            }

        } catch (\Throwable $e) {
            Log::error("AI analysis failed for report #{$report->id}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Run hotspot clustering for a zone.
     * Called by scheduled command.
     */
    public function clusterZone(Zone $zone): array
    {
        $reports = Report::where('zone_id', $zone->id)
            ->where('status', 'verified')
            ->whereNotNull('latitude')
            ->get(['id', 'title', 'description', 'category', 'district', 'latitude', 'longitude', 'created_at'])
            ->toArray();

        if (count($reports) < 3) return [];

        try {
            $response = Http::withHeaders([
                'X-Secret-Key' => $this->secretKey,
                'Accept'       => 'application/json',
            ])->post("{$this->baseUrl}/api/cluster", [
                'zone_id' => $zone->id,
                'reports' => $reports,
            ]);

            if ($response->successful()) {
                return $response->json('hotspots', []);
            }

        } catch (\Throwable $e) {
            Log::error("AI clustering failed for zone #{$zone->id}: " . $e->getMessage());
        }

        return [];
    }
}