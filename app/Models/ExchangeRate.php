<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_from', 'currency_to', 'rate', 'is_active', 'updated_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'rate'      => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public static function getRate(string $from, string $to = 'LKR'): float
    {
        $rate = static::where('currency_from', $from)
            ->where('currency_to', $to)
            ->where('is_active', true)
            ->value('rate');

        // Fallback defaults if no DB rate exists
        return (float) ($rate ?? match($from) {
            'USD' => 295.00,
            'EUR' => 330.00,
            default => 1.00,
        });
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
