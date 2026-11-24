<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Api\Auth\RangerAuthController;
use App\Http\Controllers\AdminController;



//landing

Route::get('/', function () {
    return view('landing');
});

// dashboard

Route::get('/dashboard', function () {
    return view('dashboard');
});


// Admin one-time token link → shows login form
Route::get('/admin/auth/{token}', [AdminAuthController::class, 'authenticate']);

// Admin password submit
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Admin logout — no session needed to log out
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

// Admin session check API — used by React
Route::get('/admin/session', [AdminAuthController::class, 'session']);

// Admin SPA — protected, redirect to landing if no session
Route::middleware(['web', 'admin.session'])->group(function () {
    Route::get('/admin/{any?}', function () {
        return view('admin');
    })->where('any', '^(?!auth|login|logout|session).*$');
    // NGO Alerts page
Route::get('/ngo-alerts', [AdminController::class, 'ngoAlerts'])->name('admin.ngo-alerts');

// Rangers management page
Route::get('/rangers', [AdminController::class, 'rangers'])->name('admin.rangers');

// Toggle ranger active/suspended status
Route::patch('/rangers/{id}/toggle', [AdminController::class, 'toggleRanger'])->name('admin.rangers.toggle');
});

Route::get('/ranger/verify/{token}',         [RangerAuthController::class, 'verify']);
Route::post('/ranger/resend-verification',   [RangerAuthController::class, 'resendVerification']);


// Report submission page — "Submit Anonymous Report" from landing page
Route::get('/report', function () {
    return view('report');
})->name('report');

// Report status checker — "Check my report status"
Route::get('/status', function () {
    return view('status');
})->name('status');

// Ranger login + register combined page
Route::get('/ranger/login',    function () { return view('ranger-auth'); })->name('ranger.login');
Route::get('/ranger/register', function () { return view('ranger-auth'); })->name('ranger.register');

// Ranger profile page (auth check happens client-side via localStorage)
Route::get('/ranger/profile', function () {
    return view('ranger-profile');
})->name('ranger.profile');

// Public intel feed standalone page
Route::get('/feed', function () {
    return view('feed');
})->name('feed');

// Public leaderboard page
Route::get('/leaderboard', function () {
    return view('leaderboard');
})->name('leaderboard');

