<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'verified_by',      // admin user_id
        'action',           // 'verified', 'rejected', 'escalated', 'flagged'
        'notes',
        'severity_assigned',// 'low', 'medium', 'high', 'critical'
        'ngo_recommended',  // boolean — admin recommends NGO alert
        'zone_score_delta', // how much this verification changes zone score
    ];

    protected $casts = [
        'ngo_recommended'  => 'boolean',
        'zone_score_delta' => 'integer',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}