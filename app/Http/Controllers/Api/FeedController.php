<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * This function runs when someone visits GET /api/feed
     *
     * It returns a paginated list of reports for the public scrollable feed.
     * 15 reports per page.
     *
     * Optional query parameters:
     *   ?page=2              → go to page 2
     *   ?category=corruption → filter by category
     *   ?district=Dhaka      → filter by district name
     *
     * Privacy rules applied here:
     * - No witness_token is ever returned
     * - No user identity is ever returned
     * - Coordinates are rounded to 2 decimal places (~1km fuzzing)
     * - Description is truncated to 200 characters for preview
     */
    public function index(Request $request)
    {
        $query = Report::with(['zone:id,name,danger_score', 'user:id,username', 'evidenceFiles'])
            ->whereIn('status', ['pending', 'verified', 'resolved', 'investigating'])
            ->orderByDesc('created_at');

        // If ?category=corruption is passed, filter by that category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // If ?district=Dhaka is passed, filter by zone name
        if ($request->filled('district')) {
            $query->whereHas('zone', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->district . '%');
            });
        }

        // Get 15 results per page
        $reports = $query->paginate(15);

        // Format each report for safe public display
        $items = $reports->getCollection()->map(function ($report) {
            return $this->formatFeedItem($report);
        });

        return response()->json([
            'success'      => true,
            'data'         => $items,
            'current_page' => $reports->currentPage(),
            'last_page'    => $reports->lastPage(),
            'total'        => $reports->total(),
            'has_more'     => $reports->hasMorePages(),
        ]);
    }

    /**
     * Takes a Report model and returns a safe, public-facing array.
     * All sensitive data is stripped or anonymised here.
     */
    private function formatFeedItem(Report $report): array
    {
        // Round coordinates to 2 decimal places for ~1km privacy fuzzing
        $lat = $report->latitude  ? round((float)$report->latitude,  2) : null;
        $lng = $report->longitude ? round((float)$report->longitude, 2) : null;

        // Show only the first 200 characters of the description
        $preview = mb_strlen($report->description) > 200
            ? mb_substr($report->description, 0, 197) . '...'
            : $report->description;

        // Show "Anonymous Witness" or "@username" — never email or real name
        $reporterLabel = 'Anonymous Witness';
        if ($report->user_id && $report->user) {
            $reporterLabel = '@' . $report->user->username;
        }

        // "3 hours ago", "2 days ago", etc.
        $timeAgo = $report->created_at->diffForHumans();

        // Convert AI severity score (0.0 to 1.0) into a human-readable label
        $severityLabel = match (true) {
            ($report->ai_severity ?? 0) >= 0.8 => 'CRITICAL',
            ($report->ai_severity ?? 0) >= 0.6 => 'HIGH',
            ($report->ai_severity ?? 0) >= 0.4 => 'MEDIUM',
            default                             => 'LOW',
        };

        // Color for severity label
        $severityColor = match ($severityLabel) {
            'CRITICAL' => '#FF0000',
            'HIGH'     => '#FF6B00',
            'MEDIUM'   => '#FFD700',
            default    => '#00FF88',
        };

        return [
            'id'               => $report->id,
            'tracking_token'   => $report->tracking_token, // safe to share — no identity
            'category'         => $report->category,
            'category_label'   => $this->categoryLabel($report->category),
            'description'      => $preview,
            'status'           => $report->status,
            'status_label'     => ucfirst($report->status),
            'reporter'         => $reporterLabel,
            'time_ago'         => $timeAgo,
            'created_at'       => $report->created_at->toISOString(),
            'district'         => $report->zone?->name ?? $report->district_name,
            'specific_location'=> $report->specific_location,
            'latitude'         => $lat,
            'longitude'        => $lng,
            'ai_category'      => $report->ai_category,
            'ai_severity'      => $report->ai_severity
                ? round((float)$report->ai_severity, 2)
                : null,
            'severity_label'   => $report->ai_severity ? $severityLabel : null,
            'severity_color'   => $report->ai_severity ? $severityColor  : null,
            'zone'             => $report->zone ? [
                'id'           => $report->zone->id,
                'name'         => $report->zone->name,
                'danger_score' => $report->zone->danger_score,
            ] : null,
            'evidence_count'   => $report->evidenceFiles?->count() ?? 0,
            'has_evidence'     => ($report->evidenceFiles?->count() ?? 0) > 0,
        ];
    }

    /**
     * Returns a bilingual (Bangla + English) label for each crime category.
     * This is what shows in the feed card header.
     */
    private function categoryLabel(string $category): string
    {
        return match ($category) {
            'corruption'        => 'দুর্নীতি · Corruption',
            'child_abuse'       => 'শিশু নির্যাতন · Child Abuse',
            'bribery'           => 'ঘুষ · Bribery',
            'fraud'             => 'প্রতারণা · Fraud',
            'harassment'        => 'হয়রানি · Harassment',
            'drug_trafficking'  => 'মাদক · Drug Trafficking',
            'violence'          => 'সহিংসতা · Violence',
            'theft'             => 'চুরি · Theft',
            'extortion'         => 'চাঁদাবাজি · Extortion',
            'land_grabbing'     => 'ভূমি দখল · Land Grabbing',
            'police_misconduct' => 'পুলিশের অসদাচরণ · Police Misconduct',
            'other'             => 'অন্যান্য · Other',
            default             => ucfirst(str_replace('_', ' ', $category)),
        };
    }
}