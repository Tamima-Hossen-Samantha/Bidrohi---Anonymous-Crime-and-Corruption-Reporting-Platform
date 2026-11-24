<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\AdminLoginToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateAdminLink extends Command
{
    protected $signature   = 'admin:link';
    protected $description = 'Generate a new one-time admin login URL';

    public function handle(): void
    {
        $this->line('');
        $this->line('  BIDROHI — ADMIN LINK GENERATOR');
        $this->line('  ───────────────────────────────');
        $this->line('');

        $email    = $this->ask('  Admin email');
        $password = $this->secret('  Admin password');

        $user = User::where('email', $email)
                    ->where('role', 'admin')
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->error('  ✗ Invalid credentials.');
            return;
        }

        if (!$user->is_active) {
            $this->error('  ✗ Admin account is suspended.');
            return;
        }

        // Revoke all old tokens for this admin
        AdminLoginToken::where('user_id', $user->id)->delete();

        // Generate fresh token
        $token   = Str::random(64);
        $expires = now()->addMinutes(15);

        AdminLoginToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $token),
            'expires_at' => $expires,
            'used'       => false,
        ]);

        $url = url("/admin/auth/{$token}");

        $this->line('');
        $this->line('  ✓ New login URL generated.');
        $this->line('');
        $this->line('  ');
        $this->line(' ONE-TIME LOGIN URL — EXPIRES IN 15 MINUTES  ');
        
        $this->line("  {$url}");
    
        $this->line('  ⚠  Single use only. Expires on logout.         ');
       
        $this->line('');
    }
}