<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    protected $fillable = [
        'user_id', 'release_code', 'period_start', 'period_end',
        'transaction_count', 'total_usd', 'total_eur', 'total_lkr',
        'exchange_rate_usd_lkr', 'exchange_rate_eur_lkr',
        'bank_name', 'bank_account', 'bank_account_holder',
        'status', 'scheduled_at', 'processed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start'  => 'date',
            'period_end'    => 'date',
            'scheduled_at'  => 'datetime',
            'processed_at'  => 'datetime',
            'total_usd'     => 'decimal:2',
            'total_eur'     => 'decimal:2',
            'total_lkr'     => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'scheduled'  => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed'  => 'bg-green-100 text-green-800',
            'failed'     => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }

    public static function generateCode(int $userId): string
    {
        $count = static::where('user_id', $userId)->count() + 1;
        return 'REL-' . date('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function nextReleaseDate(): \Carbon\Carbon
    {
        $now  = now();
        $day1 = $now->copy()->startOfMonth();
        $day16 = $now->copy()->startOfMonth()->addDays(15);

        if ($now->day < 16) {
            return $day16;
        }
        return $day1->addMonth();
    }
}
