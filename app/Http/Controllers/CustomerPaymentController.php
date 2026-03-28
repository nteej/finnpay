<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\PaymentReference;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function show(Request $request, string $reference)
    {
        $ref = PaymentReference::with('user')
            ->where('reference_number', $reference)
            ->whereIn('status', ['active', 'paid']) // 'paid' needed for PayPal return redirect
            ->firstOrFail();

        $returnStatus = $request->query('status'); // 'success' | 'cancel' | null

        return view('customer.pay', compact('ref', 'returnStatus'));
    }

    public function pay(Request $request, string $reference)
    {
        $ref = PaymentReference::with('user')
            ->where('reference_number', $reference)
            ->where('status', 'active')
            ->firstOrFail();

        $data = $request->validate([
            'payer_name'  => 'required|string|max:255',
            'payer_email' => 'required|email',
            'amount'      => 'required|numeric|min:1',
            'currency'    => 'required|in:USD,EUR',
        ]);

        $isEur   = $data['currency'] === 'EUR';
        $amount  = (float) $data['amount'];
        $feeRate = 0.049; // ~4.9% PayPal fee approximation
        $fee     = round($amount * $feeRate, 2);
        $final   = round($amount - $fee, 2);

        // LKR conversion using live admin-managed rates
        $lkrRate = ExchangeRate::getRate($data['currency']);
        $finalLkr = round($final * $lkrRate, 2);

        Transaction::create([
            'user_id'              => $ref->user_id,
            'payment_reference_id' => $ref->id,
            'payer_name'           => $data['payer_name'],
            'payer_email'          => $data['payer_email'],
            'currency_type'        => $data['currency'],
            'amount_usd'           => $isEur ? null : $amount,
            'amount_eur'           => $isEur ? $amount : null,
            'fee_usd'              => $isEur ? 0 : $fee,
            'fee_eur'              => $isEur ? $fee : 0,
            'final_usd'            => $isEur ? null : $final,
            'final_eur'            => $isEur ? $final : null,
            'final_lkr'            => $finalLkr,
            'lkr_rate'             => $lkrRate,
            'status'               => 'cleared',
            'received_at'          => now()->toDateString(),
        ]);

        // Mark reference as paid if amount_requested was set
        if ($ref->amount_requested && $ref->transactions()->sum('final_' . strtolower($data['currency'])) >= $ref->amount_requested) {
            $ref->update(['status' => 'paid']);
        }

        return redirect()->route('customer.pay', $reference)
            ->with('success', 'Payment of ' . $data['currency'] . ' ' . number_format($amount, 2) . ' received successfully! Reference: ' . $reference);
    }
}
