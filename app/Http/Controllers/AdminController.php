<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NgoAlert;
use App\Models\User;
use App\Models\Ranger;
use Carbon\Carbon;


class AdminController extends Controller
{
    

    public function ngoAlerts(Request $request)
    {
        // Start with all alerts, newest first, with the related report and zone loaded
        $query = \App\Models\NgoAlert::with(['report.zone'])->latest();

        // If admin filtered by status (pending / sent / failed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // If admin filtered by crime category
        if ($request->filled('category')) {
            $query->whereHas('report', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // If admin typed in the search box
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ngo_name', 'LIKE', "%$s%")
                  ->orWhereHas('report.zone', function ($z) use ($s) {
                      $z->where('name', 'LIKE', "%$s%");
                  });
            });
        }

        // Paginate: 25 alerts per page
        $alerts = $query->paginate(25)->withQueryString();

        // Stats for the four stat cards at the top of the page
        $stats = [
            'pending'   => \App\Models\NgoAlert::where('status', 'pending')->count(),
            'sent'      => \App\Models\NgoAlert::where('status', 'sent')->count(),
            'failed'    => \App\Models\NgoAlert::where('status', 'failed')->count(),
            'this_week' => \App\Models\NgoAlert::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];

       return response()->json([
    'success'  => true,
    'data'     => $alerts->items(),
    'has_more' => $alerts->hasMorePages(),
    'stats'    => $stats,
]);
    }

    /**
     * GET /admin/rangers
     *
     * Shows the Rangers management page.
     * Lists all users with role=ranger, with optional filters.
     * Also passes the rank distribution counts for the 5 rank cards.
     */
    public function rangers(Request $request)
    {
        // Get all users who are rangers, with their ranger profile and zone loaded
        $query = User::with(['ranger.zone'])->where('role', 'ranger')->latest();

        // Filter by email verification status
        if ($request->filled('verified')) {
            if ($request->verified === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by rank tier
        if ($request->filled('rank')) {
            $query->whereHas('ranger', function ($q) use ($request) {
                $q->where('rank', $request->rank);
            });
        }

        // Search by username or email
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'LIKE', "%$s%")
                  ->orWhere('email', 'LIKE', "%$s%");
            });
        }

        // Paginate: 25 rangers per page
        $rangers = $query->paginate(25)->withQueryString();

        // Count rangers at each rank tier for the 5 rank distribution cards
        $rankStats = \App\Models\Ranger::selectRaw('rank, COUNT(*) as cnt')
            ->groupBy('rank')
            ->pluck('cnt', 'rank')
            ->toArray();

       return response()->json([
    'success'  => true,
    'data'     => $rangers->items(),
    'has_more' => $rangers->hasMorePages(),
]);
    }

    /**
     * PATCH /admin/rangers/{id}/toggle
     *
     * Toggles a ranger between active and suspended.
     * This is called when admin clicks the SUSPEND or RESTORE button.
     */
    public function toggleRanger(Request $request, int $userId)
    {
        $user = User::where('role', 'ranger')->findOrFail($userId);

        // Flip the is_active flag
        $user->is_active = !($user->is_active ?? true);
        $user->save();

        $status = $user->is_active ? 'restored' : 'suspended';

        return redirect()->back()->with('success', "Ranger @{$user->username} has been {$status}.");
    }
}
