<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Hotspot;
use App\Models\User;
use App\Models\NgoAlert;
use App\Models\Zone;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('platform_stats', 60, function () {

            $now     = Carbon::now();
            $last24h = $now->copy()->subHours(24);
            $last7d  = $now->copy()->subDays(7);

            $totalReports = Report::count();

            $reportsToday = Report::where('created_at', '>=', $last24h)->count();

            $reportsThisWeek = Report::where('created_at', '>=', $last7d)->count();

            $activeHotspots = Hotspot::where('updated_at', '>=', $now->copy()->subDays(30))
                ->count();

            // FIX: 'resolved' doesn't exist — correct values are 'verified' and 'confirmed'
            $verifiedCases = Report::whereIn('status', ['verified', 'confirmed'])->count();

            $totalRangers = User::where('role', 'ranger')
                ->whereNotNull('email_verified_at')
                ->count();

            $ngoAlerts = NgoAlert::count();

            $activeZones = Zone::whereHas('reports')->count();

            // FIX: 'category' doesn't exist — correct column is 'crime_type'
            $categoryBreakdown = Report::selectRaw('crime_type as category, COUNT(*) as count')
                ->groupBy('crime_type')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'category')
                ->toArray();

            // FIX: 'danger_score' doesn't exist — correct column is 'score'
            $topZone = Zone::orderByDesc('score')->first();

            $dailyTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $day = $now->copy()->subDays($i);
                $dailyTrend[] = [
                    'date'  => $day->format('M d'),
                    'count' => Report::whereDate('created_at', $day->toDateString())->count(),
                ];
            }

            return [
                'total_reports'      => $totalReports,
                'reports_today'      => $reportsToday,
                'reports_this_week'  => $reportsThisWeek,
                'active_hotspots'    => $activeHotspots,
                'verified_cases'     => $verifiedCases,
                'total_rangers'      => $totalRangers,
                'ngo_alerts_sent'    => $ngoAlerts,
                'zones_monitored'    => $activeZones,
                'total_zones'        => 64,
                'category_breakdown' => $categoryBreakdown,
                'top_danger_zone'    => $topZone ? [
                    'name'         => $topZone->name,
                    'danger_score' => $topZone->score, // FIX: was danger_score
                ] : null,
                'daily_trend'        => $dailyTrend,
                'last_updated'       => $now->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}