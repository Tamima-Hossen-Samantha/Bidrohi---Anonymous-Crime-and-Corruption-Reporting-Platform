<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranger extends Model
{
    use HasFactory;

    // Rank thresholds
    const RANKS = [
        ['name' => 'নাগরিক',  'name_en' => 'Nagorik',   'min' => 0],
        ['name' => 'সাক্ষী',   'name_en' => 'Sakkhi',    'min' => 50],
        ['name' => 'প্রতিজ্ঞা', 'name_en' => 'Protijgya', 'min' => 150],
        ['name' => 'রক্ষক',   'name_en' => 'Rokkho',    'min' => 400],
        ['name' => 'বিদ্রোহী', 'name_en' => 'Bidrohi',   'min' => 1000],
    ];

    protected $fillable = [
        'user_id',
        'district',         // home district
        'points',
        'rank',             // current rank name (Bangla)
        'rank_en',          // current rank name (English)
        'reports_submitted',
        'reports_verified',
        'zones_controlled',
        'badge_data',       // JSON array of earned badges
        'is_verified',      // admin-verified ranger
        'last_active_at',
    ];

    protected $casts = [
        'points'            => 'integer',
        'reports_submitted' => 'integer',
        'reports_verified'  => 'integer',
        'zones_controlled'  => 'integer',
        'badge_data'        => 'array',
        'is_verified'       => 'boolean',
        'last_active_at'    => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actions()
    {
        return $this->hasMany(RangerAction::class);
    }

    public function controlledZones()
    {
        return $this->hasMany(Zone::class, 'controlling_ranger_id');
    }

    // Compute rank from points
    public function computeRank(): array
    {
        $current = self::RANKS[0];
        foreach (self::RANKS as $rank) {
            if ($this->points >= $rank['min']) {
                $current = $rank;
            }
        }
        return $current;
    }

    public function updateRank(): void
    {
        $rank = $this->computeRank();
        $this->update([
            'rank'    => $rank['name'],
            'rank_en' => $rank['name_en'],
        ]);
    }

    public function addPoints(int $pts): void
    {
        $this->increment('points', $pts);
        $this->updateRank();
    }

    // Points to next rank
    public function pointsToNextRank(): ?int
    {
        foreach (self::RANKS as $rank) {
            if ($rank['min'] > $this->points) {
                return $rank['min'] - $this->points;
            }
        }
        return null; // Already at max rank
    }
}