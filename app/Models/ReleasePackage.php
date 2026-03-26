<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReleasePackage extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color',
        'releases_per_month', 'release_day_1', 'release_day_2',
        'minimum_balance_lkr', 'allow_manual_release',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'allow_manual_release' => 'boolean',
        'is_active'            => 'boolean',
    ];

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function nextReleaseDate(): Carbon
    {
        $now  = now();
        $day1 = $now->copy()->startOfMonth()->addDays($this->release_day_1 - 1);

        if ($this->releases_per_month === 1 || ! $this->release_day_2) {
            return $now->lt($day1)
                ? $day1
                : $now->copy()->addMonth()->startOfMonth()->addDays($this->release_day_1 - 1);
        }

        $day2 = $now->copy()->startOfMonth()->addDays($this->release_day_2 - 1);
        if ($now->lt($day1)) return $day1;
        if ($now->lt($day2)) return $day2;

        return $now->copy()->addMonth()->startOfMonth()->addDays($this->release_day_1 - 1);
    }

    public function scheduleLabel(): string
    {
        if ($this->releases_per_month === 1) {
            return 'Once/month (' . ordinal_suffix($this->release_day_1) . ')';
        }

        return 'Twice/month (' . ordinal_suffix($this->release_day_1) . ' & ' . ordinal_suffix($this->release_day_2) . ')';
    }
}

if (! function_exists('ordinal_suffix')) {
    function ordinal_suffix(int $n): string
    {
        $s = ['th', 'st', 'nd', 'rd'];
        $v = $n % 100;
        return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
    }
}
