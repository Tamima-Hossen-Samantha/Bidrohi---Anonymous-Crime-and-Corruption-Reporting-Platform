<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',             // District name (e.g., "Dhaka", "Chittagong")
        'name_bn',          // Bangla name
        'division',
        'division_bn',
        'status',           // 'green', 'yellow', 'red'
        'score',            // 0–100 danger score
        'report_count',
        'verified_count',
        'ranger_count',
        'latitude',
        'longitude',
        'controlling_ranger_id', // Top ranger in this zone
    ];

    protected $casts = [
        'score'          => 'integer',
        'report_count'   => 'integer',
        'verified_count' => 'integer',
        'ranger_count'   => 'integer',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    // Relationships
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function controllingRanger()
    {
        return $this->belongsTo(Ranger::class, 'controlling_ranger_id');
    }

    // Auto-compute status from score
    public function computeStatus(): string
    {
        if ($this->score >= 70) return 'red';
        if ($this->score >= 40) return 'yellow';
        return 'green';
    }

    public function updateStatus(): void
    {
        $this->update(['status' => $this->computeStatus()]);
    }
}