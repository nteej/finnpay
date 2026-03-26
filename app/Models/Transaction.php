<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'payment_reference_id', 'release_id',
        'payer_name', 'payer_email', 'currency_type',
        'amount_usd', 'amount_eur', 'fee_usd', 'fee_eur',
        'final_usd', 'final_eur', 'final_lkr',
        'cv_rate', 'lkr_rate', 'paypal_transaction_id',
        'status', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at'  => 'date',
            'amount_usd'   => 'decimal:2',
            'amount_eur'   => 'decimal:2',
            'fee_usd'      => 'decimal:2',
            'fee_eur'      => 'decimal:2',
            'final_usd'    => 'decimal:2',
            'final_eur'    => 'decimal:2',
            'final_lkr'    => 'decimal:2',
            'cv_rate'      => 'decimal:4',
            'lkr_rate'     => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentReference()
    {
        return $this->belongsTo(PaymentReference::class);
    }

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function getDisplayAmountAttribute(): string
    {
        if ($this->currency_type === 'EUR') {
            return '€' . number_format((float) $this->final_eur, 2);
        }
        return '$' . number_format((float) $this->final_usd, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-yellow-100 text-yellow-800',
            'cleared'  => 'bg-blue-100 text-blue-800',
            'released' => 'bg-green-100 text-green-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }
}
