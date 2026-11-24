<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ranger;
use App\Mail\RangerVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RangerAuthController extends Controller
{
    /**
     * POST /api/ranger/register
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'district' => 'required|string|max:100',
        ]);

        // Generate verification token
        $verificationToken   = Str::random(64);
        $verificationExpires = now()->addHours(24);

        $user = User::create([
            'username'                  => $request->username,
            'email'                     => $request->email,
            'password'                  => Hash::make($request->password),
            'role'                      => 'ranger',
            'is_active'                 => false, // inactive until verified
            'verification_token'        => hash('sha256', $verificationToken),
            'verification_expires_at'   => $verificationExpires,
        ]);

        // Create ranger profile
        Ranger::create([
            'user_id'  => $user->id,
            'district' => $request->district,
            'points'   => 0,
            'rank'     => 'নাগরিক',
            'rank_en'  => 'Nagorik',
        ]);

        // Send verification email
        $verificationUrl = url("/api/ranger/verify/{$verificationToken}");

        Mail::to($user->email)->send(
            new RangerVerificationMail($user, $verificationUrl)
        );

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please check your email to verify your account.',
            'data'    => [
                'username' => $user->username,
                'email'    => $user->email,
                'verified' => false,
            ],
        ], 201);
    }

    /**
     * GET /api/ranger/verify/{token}
     * Email verification link
     */
    public function verify(string $token): JsonResponse
    {
        $hashed = hash('sha256', $token);

        $user = User::where('verification_token', $hashed)
                    ->where('role', 'ranger')
                    ->whereNull('email_verified_at')
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification link.',
            ], 400);
        }

        if ($user->verification_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification link has expired. Please register again.',
            ], 400);
        }

        // Activate account
        $user->update([
            'email_verified_at'        => now(),
            'is_active'                => true,
            'verification_token'       => null,
            'verification_expires_at'  => null,
        ]);

        // Redirect to a success page or return JSON
        return response()->json([
            'success' => true,
            'message' => 'ইমেইল যাচাই সম্পন্ন। আপনি এখন লগইন করতে পারবেন। | Email verified. You can now log in.',
        ]);
    }

    /**
     * POST /api/ranger/resend-verification
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
                    ->where('role', 'ranger')
                    ->whereNull('email_verified_at')
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No unverified account found with this email.',
            ], 404);
        }

        // Generate new token
        $token   = Str::random(64);
        $expires = now()->addHours(24);

        $user->update([
            'verification_token'      => hash('sha256', $token),
            'verification_expires_at' => $expires,
        ]);

        $url = url("/api/ranger/verify/{$token}");
        Mail::to($user->email)->send(new RangerVerificationMail($user, $url));

        return response()->json([
            'success' => true,
            'message' => 'Verification email resent. Please check your inbox.',
        ]);
    }

    /**
     * POST /api/ranger/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
                    ->where('role', 'ranger')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Check email verified
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email before logging in. Check your inbox.',
                'code'    => 'EMAIL_NOT_VERIFIED',
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended.',
            ], 403);
        }

        $user->tokens()->delete();
        $token  = $user->createToken('ranger-token')->plainTextToken;
        $ranger = $user->ranger;
        $ranger->update(['last_active_at' => now()]);

        return response()->json([
            'success' => true,
            'data'    => [
                'token'  => $token,
                'user'   => [
                    'id'       => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
                'ranger' => [
                    'id'                  => $ranger->id,
                    'district'            => $ranger->district,
                    'points'              => $ranger->points,
                    'rank'                => $ranger->rank,
                    'rank_en'             => $ranger->rank_en,
                    'reports_submitted'   => $ranger->reports_submitted,
                    'points_to_next_rank' => $ranger->pointsToNextRank(),
                ],
            ],
        ]);
    }

    /**
     * POST /api/ranger/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }

    /**
     * GET /api/ranger/me
     */
    public function me(Request $request): JsonResponse
    {
        $user   = $request->user();
        $ranger = $user->ranger;

        return response()->json([
            'success' => true,
            'data'    => [
                'user'   => [
                    'id'       => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
                'ranger' => [
                    'id'                  => $ranger->id,
                    'district'            => $ranger->district,
                    'points'              => $ranger->points,
                    'rank'                => $ranger->rank,
                    'rank_en'             => $ranger->rank_en,
                    'reports_submitted'   => $ranger->reports_submitted,
                    'reports_verified'    => $ranger->reports_verified,
                    'zones_controlled'    => $ranger->zones_controlled,
                    'is_verified'         => $ranger->is_verified,
                    'points_to_next_rank' => $ranger->pointsToNextRank(),
                    'badge_data'          => $ranger->badge_data,
                ],
            ],
        ]);
    }
}