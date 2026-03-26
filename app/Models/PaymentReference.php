<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReference extends Model
{
    protected $fillable = [
        'user_id', 'reference_number', 'title', 'notes',
        'amount_requested', 'currency', 'status', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'       => 'datetime',
            'amount_requested' => 'decimal:2',
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

    public function totalReceived(): float
    {
        return (float) $this->transactions()->whereIn('status', ['cleared', 'released'])->sum('final_eur');
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'FP-' . date('Ym') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('reference_number', $ref)->exists());

        return $ref;
    }
}
