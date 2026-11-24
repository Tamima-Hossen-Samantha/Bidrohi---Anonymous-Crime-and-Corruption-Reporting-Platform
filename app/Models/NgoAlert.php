<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgoAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'ngo_name',
        'ngo_email',
        'ngo_type',         // 'child_protection', 'anti_corruption', 'human_rights'
        'alert_type',       // 'auto' (AI triggered) or 'manual' (admin triggered)
        'message',
        'status',           // 'sent', 'delivered', 'acknowledged', 'failed'
        'sent_at',
        'acknowledged_at',
        'response_notes',
    ];

    protected $casts = [
        'sent_at'          => 'datetime',
        'acknowledged_at'  => 'datetime',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Scopes
    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    public function scopeAutoTriggered($query)
    {
        return $query->where('alert_type', 'auto');
    }
}