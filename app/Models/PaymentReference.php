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

    public function workHistoryEntry()
    {
        return $this->hasOne(WorkHistoryEntry::class);
    }

    public function totalReceived(): float
    {
        return (float) $this->transactions()->whereIn('status', ['cleared', 'released'])->sum('final_eur');
    }

    public function paypalUrl(): string
    {
        $params = [
            'cmd'           => '_xclick',
            'business'      => config('services.paypal.business_email'),
            'currency_code' => $this->currency,
            'item_name'     => 'FinnPay Payment - ' . $this->reference_number,
            'custom'        => $this->reference_number,
            'no_shipping'   => '1',
            'return'        => route('customer.pay', $this->reference_number) . '?status=success',
            'cancel_return' => route('customer.pay', $this->reference_number) . '?status=cancel',
        ];

        if ($this->amount_requested) {
            $params['amount'] = number_format($this->amount_requested, 2, '.', '');
        }

        return 'https://www.paypal.com/cgi-bin/webscr?' . http_build_query($params);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'FP-' . date('Ym') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('reference_number', $ref)->exists());

        return $ref;
    }
}
