<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'name',             // e.g., "Motijheel Commercial Area"
        'category',         // 'corruption', 'child_abuse', 'combined'
        'latitude',
        'longitude',
        'radius_meters',    // hotspot radius for map rendering
        'intensity',        // 0–100 heatmap intensity
        'report_count',
        'last_incident_at',
        'is_active',
        'ai_generated',     // true if detected by AI clustering
        'cluster_id',       // from Python FastAPI k-means
    ];

    protected $casts = [
        'latitude'        => 'float',
        'longitude'       => 'float',
        'radius_meters'   => 'integer',
        'intensity'       => 'integer',
        'report_count'    => 'integer',
        'is_active'       => 'boolean',
        'ai_generated'    => 'boolean',
        'last_incident_at'=> 'datetime',
    ];

    // Relationships
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // Scope: only active hotspots for map rendering
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHighIntensity($query, int $threshold = 70)
    {
        return $query->where('intensity', '>=', $threshold);
    }
}