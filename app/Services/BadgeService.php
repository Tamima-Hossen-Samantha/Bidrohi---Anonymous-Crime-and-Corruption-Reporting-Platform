<?php

namespace App\Services;

use App\Models\Ranger;

class BadgeService
{
    const BADGES = [
        'first_blood'    => ['name' => 'First Blood',     'name_bn' => 'প্রথম সাক্ষী',    'condition' => 'reports_submitted >= 1'],
        'truth_seeker'   => ['name' => 'Truth Seeker',    'name_bn' => 'সত্যের সন্ধানী',  'condition' => 'reports_verified >= 5'],
        'zone_guardian'  => ['name' => 'Zone Guardian',   'name_bn' => 'অঞ্চল রক্ষক',     'condition' => 'zones_controlled >= 1'],
        'evidence_vault' => ['name' => 'Evidence Vault',  'name_bn' => 'প্রমাণ ভান্ডার',  'condition' => 'reports_submitted >= 10'],
        'district_hero'  => ['name' => 'District Hero',   'name_bn' => 'জেলার বীর',        'condition' => 'zones_controlled >= 3'],
        'bidrohi_elite'  => ['name' => 'Bidrohi Elite',   'name_bn' => 'বিদ্রোহী এলিট',   'condition' => 'points >= 1000'],
        'century'        => ['name' => 'Century',         'name_bn' => 'শতক',              'condition' => 'reports_submitted >= 100'],
    ];

    /**
     * Check and award any new badges the ranger has earned.
     * Returns array of newly awarded badges.
     */
    public function checkAndAward(Ranger $ranger): array
    {
        $current  = $ranger->badge_data ?? [];
        $newBadges = [];

        foreach (self::BADGES as $key => $badge) {
            // Already has this badge
            if (in_array($key, array_column($current, 'key'))) continue;

            if ($this->meetsCondition($ranger, $badge['condition'])) {
                $newBadge    = array_merge($badge, ['key' => $key, 'earned_at' => now()->toDateTimeString()]);
                $current[]   = $newBadge;
                $newBadges[] = $newBadge;
            }
        }

        if (!empty($newBadges)) {
            $ranger->update(['badge_data' => $current]);
        }

        return $newBadges;
    }

    private function meetsCondition(Ranger $ranger, string $condition): bool
    {
        // Parse simple conditions like "reports_submitted >= 5"
        preg_match('/(\w+)\s*(>=|<=|==|>|<)\s*(\d+)/', $condition, $matches);

        if (count($matches) < 4) return false;

        [, $field, $operator, $value] = $matches;
        $actual = $ranger->$field ?? 0;

        return match($operator) {
            '>='  => $actual >= (int)$value,
            '<='  => $actual <= (int)$value,
            '=='  => $actual == (int)$value,
            '>'   => $actual >  (int)$value,
            '<'   => $actual <  (int)$value,
            default => false,
        };
    }
}