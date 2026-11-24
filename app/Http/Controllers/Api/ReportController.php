<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitReportRequest;
use App\Models\Report;
use App\Models\EvidenceFile;
use App\Models\Zone;
use App\Models\RangerAction;
use App\Services\AnonymousTokenService;
use App\Services\EvidenceEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;



class ReportController extends Controller
{
    public function __construct(
        protected AnonymousTokenService    $tokenService,
        protected EvidenceEncryptionService $encryptionService,
    ) {}

    /**
     * POST /api/reports
     * Works for both anonymous witnesses and logged-in Rangers.
     */
    public function store(SubmitReportRequest $request): JsonResponse
    {
        $user = $request->user('sanctum'); // null if anonymous

        DB::beginTransaction();

        try {
            // ── 1. Resolve tracking token ───────────────────────────
            $trackingToken = null;
            $isAnonymous   = true;

            if ($user && $user->isRanger()) {
                // Ranger submission — linked to account
                $isAnonymous   = false;
                $trackingToken = null;
            } else {
                // Anonymous witness
                if ($request->tracking_token && $this->tokenService->exists($request->tracking_token)) {
                    $trackingToken = $request->tracking_token;
                } else {
                    // Auto-generate a new token for them
                    $generated     = $this->tokenService->generate();
                    $trackingToken = $generated['tracking_token'];
                }
            }

            // ── 2. Resolve zone ─────────────────────────────────────
            $zone = Zone::where('name', $request->district)
                        ->orWhere('name_bn', $request->district)
                        ->first();

            // ── 3. Create report ────────────────────────────────────
            $report = Report::create([
                'tracking_token' => $trackingToken,
                'user_id'        => $user?->id,
                'zone_id'        => $zone?->id,
                'category'       => $request->category,
                'title'          => $request->title,
                'description'    => $request->description,
                'district'       => $request->district,
                'upazila'        => $request->upazila,
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'status'         => 'pending',
                'severity'       => 'low', // AI will update this
                'is_anonymous'   => $isAnonymous,
                'ai_processed'   => false,
                'ngo_alerted'    => false,
            ]);

            // ── 4. Encrypt + store evidence files ───────────────────
            $uploadedFiles = [];
$evidenceFiles = $request->file('files') ?? $request->file('evidence') ?? [];
if (!empty($evidenceFiles)) {
    foreach ($evidenceFiles as $file) {
                    $encrypted = $this->encryptionService->encrypt($file, $report->id);

                    $evidenceFile = EvidenceFile::create([
                        'report_id'      => $report->id,
                        'file_type'      => $encrypted['file_type'],
                        'original_name'  => $encrypted['original_name'],
                        'stored_name'    => $encrypted['stored_name'],
                        'mime_type'      => $encrypted['mime_type'],
                        'file_size'      => $encrypted['file_size'],
                        'checksum'       => $encrypted['checksum'],
                        'storage_path'   => $encrypted['storage_path'],
                        'encryption_key' => $encrypted['encryption_key'],
                        'is_encrypted'   => true,
                    ]);

                    $uploadedFiles[] = [
                        'id'        => $evidenceFile->id,
                        'file_type' => $evidenceFile->file_type,
                        'file_size' => $evidenceFile->file_size,
                    ];
                }
            }
// Find nearest thana and attach to the report
$thanaService = new \App\Services\ThanaAlertService();
if ($report->latitude && $report->longitude) {
    $thana = $thanaService->findNearestThana($report->latitude, $report->longitude);
    $report->update(['nearest_thana' => $thana['thana'] . ', ' . $thana['district']]);
}
            // ── 5. Award Ranger points ──────────────────────────────
            $rangerPoints = null;
            if ($user && $user->isRanger()) {
                $ranger = $user->ranger;
                $ranger->increment('reports_submitted');

                // Bonus if they included evidence
                $actionType = count($uploadedFiles) > 0 ? 'evidence_uploaded' : 'report_submitted';
                RangerAction::log($ranger, 'report_submitted', $report->id);

                if (count($uploadedFiles) > 0) {
                    RangerAction::log($ranger, 'evidence_uploaded', $report->id);
                }

                // First report ever bonus
                if ($ranger->reports_submitted === 1) {
                    RangerAction::log($ranger, 'first_report', $report->id);
                }

                $ranger->refresh();
                $rangerPoints = [
                    'points'              => $ranger->points,
                    'rank'                => $ranger->rank,
                    'rank_en'             => $ranger->rank_en,
                    'points_to_next_rank' => $ranger->pointsToNextRank(),
                ];
            }

            // ── 6. Update zone report count ─────────────────────────
            if ($zone) {
                $zone->increment('report_count');
            }

            DB::commit();

            // ── 7. Build response ───────────────────────────────────
            $response = [
                'success' => true,
                'message' => 'তোমার রিপোর্ট জমা হয়েছে। ধন্যবাদ — তুমি নীরব থাকোনি।',
                'data'    => [
                    'report_id'      => $report->id,
                    'status'         => $report->status,
                    'category'       => $report->category,
                    'district'       => $report->district,
                    'evidence_count' => count($uploadedFiles),
                    'evidence_files' => $uploadedFiles,
                    'submitted_at'   => $report->created_at->toDateTimeString(),
                ],
            ];

            // Include tracking token ONLY for anonymous witnesses
            if ($isAnonymous && $trackingToken) {
                $response['data']['tracking'] = [
                    'tracking_token' => $trackingToken,
                    'warning'        => 'Save this token now. We cannot recover it. There is no account linked to this report.',
                ];
            }

            // Include ranger progress update
            if ($rangerPoints) {
                $response['data']['ranger_progress'] = $rangerPoints;
            }

            return response()->json($response, 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Report submission failed. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/reports/{id}
     * Admin only — full report detail.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $report = Report::with(['evidenceFiles', 'verifications', 'ngoAlerts', 'zone'])
                        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $report,
        ]);
    }

    /**
     * GET /api/reports
     * Admin only — paginated list with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $query = Report::with(['zone'])
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->district, fn($q) => $q->where('district', $request->district))
            ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
            ->orderByDesc('created_at');

        $reports = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }


    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'status'   => 'required|in:pending,verified,rejected,escalated',
            'severity' => 'nullable|in:low,medium,high,critical',
            'notes'    => 'nullable|string|max:1000',
        ]);

        $report = Report::findOrFail($id);

        DB::beginTransaction();
        try {
            $report->update([
                'status'   => $request->status,
                'severity' => $request->severity ?? $report->severity,
            ]);

            // Log verification
            $report->verifications()->create([
                'verified_by'      => $user->id,
                'action'           => $request->status,
                'notes'            => $request->notes,
                'severity_assigned'=> $request->severity ?? $report->severity,
            ]);

            // If verified — award ranger points + update zone
            if ($request->status === 'verified' && $report->user_id) {
                $ranger = User::find($report->user_id)?->ranger;
                if ($ranger) {
                    $ranger->increment('reports_verified');
                    RangerAction::log($ranger, 'report_verified', $report->id);
                }
            }

            // Recalculate zone score
            if ($report->zone_id) {
                $zone = Zone::find($report->zone_id);
                if ($zone) {
                    $verifiedCount = Report::where('zone_id', $zone->id)
                                           ->where('status', 'verified')
                                           ->count();
                    $totalCount    = Report::where('zone_id', $zone->id)->count();

                    // Score formula: weighted by verified ratio + total volume
                    $score = min(100, ($verifiedCount * 15) + ($totalCount * 2));
                    $zone->update([
                        'score'          => $score,
                        'verified_count' => $verifiedCount,
                    ]);
                    $zone->updateStatus();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Report #{$id} marked as {$request->status}.",
                'data'    => $report->fresh(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}