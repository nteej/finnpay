<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReleaseCycleSetting extends Model
{
    protected $fillable = [
        'release_day_1', 'release_day_2', 'allow_manual_release', 'minimum_balance_lkr', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'allow_manual_release' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'release_day_1'        => 1,
            'release_day_2'        => 16,
            'allow_manual_release' => true,
            'minimum_balance_lkr'  => 0,
        ]);
    }

    public function nextReleaseDate(): Carbon
    {
        $now  = now();
        $day1 = $now->copy()->startOfMonth()->addDays($this->release_day_1 - 1);
        $day2 = $now->copy()->startOfMonth()->addDays($this->release_day_2 - 1);

        if ($now->lt($day1)) return $day1;
        if ($now->lt($day2)) return $day2;

        return $now->copy()->addMonth()->startOfMonth()->addDays($this->release_day_1 - 1);
    }
}
