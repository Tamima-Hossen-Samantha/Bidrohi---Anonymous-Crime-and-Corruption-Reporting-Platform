<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_token',   // UUID for anonymous witnesses
        'user_id',          // null if anonymous, ranger ID if logged in
        'zone_id',
        'category',         // 'corruption', 'child_abuse', 'other'
        'title',
        'description',
        'district',
        'upazila',
        'latitude',
        'longitude',
        'status',           // 'pending', 'verified', 'rejected', 'escalated'
        'severity',         // 'low', 'medium', 'high', 'critical'
        'is_anonymous',
        'ai_processed',
        'ai_tags',          // JSON
        'ai_cluster_id',
        'ngo_alerted',
    ];

    protected $hidden = [
        // Never expose these accidentally
    ];

    protected $casts = [
        'is_anonymous'  => 'boolean',
        'ai_processed'  => 'boolean',
        'ngo_alerted'   => 'boolean',
        'ai_tags'       => 'array',
        'latitude'      => 'float',
        'longitude'     => 'float',
    ];

    // Auto-generate tracking token on creation
    protected static function booted()
    {
        static::creating(function ($report) {
            if (empty($report->tracking_token)) {
                $report->tracking_token = Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withDefault();
    }

    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class);
    }

    public function verifications()
    {
        return $this->hasMany(ReportVerification::class);
    }

    public function ngoAlerts()
    {
        return $this->hasMany(NgoAlert::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeByDistrict($query, $district)
    {
        return $query->where('district', $district);
    }
}