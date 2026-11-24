<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   protected $fillable = [
    'username',
    'email',
    'password',
    'role',
    'is_active',
    'verification_token',
    'verification_expires_at',
];

protected $hidden = [
    'password',
    'remember_token',
    'verification_token',
];

protected $casts = [
    'email_verified_at'        => 'datetime',
    'verification_expires_at'  => 'datetime',
    'password'                 => 'hashed',
    'is_active'                => 'boolean',
];

    // Relationships
    public function ranger()
    {
        return $this->hasOne(Ranger::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRanger(): bool
    {
        return $this->role === 'ranger';
    }
}