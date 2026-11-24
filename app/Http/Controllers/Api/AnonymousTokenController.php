<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnonymousTokenService;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnonymousTokenController extends Controller
{
    public function __construct(protected AnonymousTokenService $tokenService) {}

    /**
     * POST /api/witness/token
     * Generate a fresh anonymous token. Call this before submitting a report.
     */
    public function generate(): JsonResponse
    {
        $tokenData = $this->tokenService->generate();

        return response()->json([
            'success' => true,
            'data'    => $tokenData,
        ], 201);
    }

    /**
     * POST /api/witness/status
     * Check report status using token + secret — no login needed.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_token' => 'required|uuid',
            'secret'         => 'required|string|size:32',
        ]);

        // Validate token + secret pair
        if (!$this->tokenService->validate($request->tracking_token, $request->secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token or secret. Please check and try again.',
            ], 401);
        }

        // Fetch their reports (no identity, just the token)
        $reports = Report::where('tracking_token', $request->tracking_token)
            ->select([
                'id',
                'tracking_token',
                'category',
                'title',
                'district',
                'status',
                'severity',
                'ai_processed',
                'ngo_alerted',
                'created_at',
                'updated_at',
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success'      => true,
            'report_count' => $reports->count(),
            'reports'      => $reports,
        ]);
    }
}