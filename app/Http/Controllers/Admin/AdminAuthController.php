<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function authenticate(string $token)
    {
        $hashed = hash('sha256', $token);

        $record = AdminLoginToken::where('token', $hashed)
                                  ->where('used', false)
                                  ->first();

        if (!$record || !$record->isValid()) {
            return redirect('/');
        }

        // Mark used immediately
        $record->update(['used' => true]);

        // Store pending user in session
        session(['admin_pending_id' => $record->user_id]);

        return view('admin-login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $pendingId = session('admin_pending_id');

        if (!$pendingId) {
            return redirect('/');
        }

        $user = User::find($pendingId);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        // Clear pending, set authenticated
        session()->forget('admin_pending_id');
        session()->put('admin_authenticated', true);
        session()->put('admin_user_id', $user->id);
        session()->put('admin_username', $user->username);
        session()->save();

        return redirect('/admin');
    }

    public function logout(Request $request)
    {
        $userId = session('admin_user_id');
        if ($userId) {
            AdminLoginToken::where('user_id', $userId)->delete();
        }

        session()->flush();
        session()->invalidate();
        session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect('/');
    }

    public function session()
    {
        if (session('admin_authenticated')) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id'       => session('admin_user_id'),
                    'username' => session('admin_username'),
                ],
            ]);
        }
        return response()->json(['authenticated' => false], 401);
    }
}