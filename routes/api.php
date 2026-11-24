<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnonymousTokenController;
use App\Http\Controllers\Api\Auth\RangerAuthController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\RangerController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\AdminController;

// Anonymous Witness Routes — No auth required

Route::prefix('witness')->group(function () {
    Route::post('/token',  [AnonymousTokenController::class, 'generate']);
    Route::post('/status', [AnonymousTokenController::class, 'checkStatus']);
});


// Ranger Auth Routes — No auth required

Route::prefix('ranger')->group(function () {
    Route::post('/register', [RangerAuthController::class, 'register']);
    Route::post('/login',    [RangerAuthController::class, 'login']);

    // Protected ranger routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [RangerAuthController::class, 'logout']);
        Route::get('/me',      [RangerAuthController::class, 'me']);
    });
});


// Admin Auth Routes

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
    });
});



// ── Reports ──────────────────────────────────────────────────────
// Anonymous OR Ranger can submit
Route::post('/reports', [ReportController::class, 'store']);

// Admin protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports',               [ReportController::class, 'index']);
    Route::get('/reports/{id}',          [ReportController::class, 'show']);
    Route::patch('/reports/{id}/status', [ReportController::class, 'updateStatus']);
});



// ── Zones (public) ────────────────────────────────────────────────
Route::get('/zones',          [ZoneController::class, 'index']);
Route::get('/zones/heatmap',  [ZoneController::class, 'heatmap']);
Route::get('/zones/{id}',     [ZoneController::class, 'show']);


// ── Leaderboard (public) ──────────────────────────────────────────
Route::prefix('leaderboard')->group(function () {
    Route::get('/',                      [LeaderboardController::class, 'global']);
    Route::get('/zones',                 [LeaderboardController::class, 'zoneControl']);
    Route::get('/ranks',                 [LeaderboardController::class, 'rankDistribution']);
    Route::get('/district/{district}',   [LeaderboardController::class, 'byDistrict']);
});

// ── Ranger profiles (public) ──────────────────────────────────────
Route::get('/rangers/{username}',        [RangerController::class, 'profile']);

// ── Ranger private routes ─────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('rangers/me')->group(function () {
    Route::get('/actions',               [RangerController::class, 'myActions']);
    Route::get('/stats',                 [RangerController::class, 'myStats']);
    Route::patch('/district',            [RangerController::class, 'updateDistrict']);
});
 
// Live platform statistics — powers the dashboard counters
Route::get('/stats', [StatsController::class, 'index']);

// Public scrollable report feed — privacy-safe, 15 per page
Route::get('/feed', [FeedController::class, 'index']);

 Route::middleware(['admin.session'])->prefix('admin')->group(function () {
    Route::get('/ngo-alerts', [AdminController::class, 'ngoAlerts']);
    Route::get('/rangers',    [AdminController::class, 'rangers']);
});