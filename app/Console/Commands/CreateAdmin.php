<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\AdminLoginToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Create an admin account and generate a one-time login URL';

    public function handle(): void
    {
       
       
        $this->line('  ADMIN CREATION');
        
        $this->line('');

        $username = $this->ask('  Enter admin username');
        $email    = $this->ask('  Enter admin email');
        $password = $this->secret('  Enter admin password (hidden)');
        $confirm  = $this->secret('  Confirm password (hidden)');

        if ($password !== $confirm) {
            $this->error('  ✗ Passwords do not match.');
            return;
        }

        if (strlen($password) < 8) {
            $this->error('  ✗ Password must be at least 8 characters.');
            return;
        }

        // Delete existing admin if exists
        User::where('role', 'admin')->where('email', $email)->delete();

        $user = User::create([
            'username'  => $username,
            'email'     => $email,
            'password'  => Hash::make($password),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Generate one-time login token
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
        $this->line('  ✓ Admin created successfully.');
        $this->line('');
        $this->line('  ');
        $this->line('  ONE-TIME LOGIN URL — EXPIRES IN 15 MINUTES ');
        $this->line('                                              ');
        $this->line(" {$url}");
        
        $this->line('  ⚠  This URL works ONCE and expires automatically.  ');
        $this->line('  ⚠  Run admin:link to generate a new one.           ');
       
        $this->line('');
    }
}