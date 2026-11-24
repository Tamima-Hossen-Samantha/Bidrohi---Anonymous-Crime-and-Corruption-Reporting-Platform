<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangerAction extends Model
{
    use HasFactory;

    // Action types and their point values
    const ACTIONS = [
        'report_submitted'  => 10,
        'report_verified'   => 25,
        'evidence_uploaded' => 15,
        'zone_captured'     => 50,
        'ngo_alerted'       => 30,
        'streak_bonus'      => 20,
        'first_report'      => 40,
    ];

    protected $fillable = [
        'ranger_id',
        'report_id',    // optional, if action is tied to a report
        'action_type',  // one of ACTIONS keys
        'points_earned',
        'description',
        'meta',         // JSON for extra context
    ];

    protected $casts = [
        'points_earned' => 'integer',
        'meta'          => 'array',
    ];

    // Relationships
    public function ranger()
    {
        return $this->belongsTo(Ranger::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class)->withDefault();
    }

    // Factory method — log an action and award points
    public static function log(Ranger $ranger, string $actionType, ?int $reportId = null, array $meta = []): self
    {
        $points = self::ACTIONS[$actionType] ?? 0;

        $action = self::create([
            'ranger_id'     => $ranger->id,
            'report_id'     => $reportId,
            'action_type'   => $actionType,
            'points_earned' => $points,
            'description'   => "Earned {$points} points for {$actionType}",
            'meta'          => $meta,
        ]);

        $ranger->addPoints($points);

        return $action;
    }
}