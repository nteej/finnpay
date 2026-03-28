<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\PaymentReference;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalIpnController extends Controller
{
    public function handle(Request $request): Response
    {
        $ipn = $request->all();

        Log::channel('paypal')->info('IPN received', $ipn);

        // Step 1 — Verify authenticity with PayPal
        if (! $this->verify($ipn)) {
            Log::channel('paypal')->warning('IPN verification failed — INVALID response from PayPal', $ipn);
            return response('INVALID', 200);
        }

        // Step 2 — Only process completed payments
        $paymentStatus = $ipn['payment_status'] ?? '';
        if ($paymentStatus !== 'Completed') {
            Log::channel('paypal')->info('IPN skipped — payment_status is not Completed', [
                'payment_status' => $paymentStatus,
                'txn_id'         => $ipn['txn_id'] ?? null,
            ]);
            return response('OK', 200);
        }

        // Step 3 — Confirm the payment came to our account
        $receiverEmail = strtolower($ipn['receiver_email'] ?? $ipn['business'] ?? '');
        $businessEmail = strtolower(config('services.paypal.business_email', ''));

        if ($receiverEmail !== $businessEmail) {
            Log::channel('paypal')->warning('IPN receiver email mismatch', [
                'expected' => $businessEmail,
                'got'      => $receiverEmail,
            ]);
            return response('OK', 200);
        }

        // Step 4 — Extract required fields
        $txnId     = $ipn['txn_id']       ?? null;
        $reference = $ipn['custom']        ?? null;
        $currency  = strtoupper($ipn['mc_currency'] ?? 'USD');
        $gross     = (float) ($ipn['mc_gross'] ?? 0);
        $fee       = abs((float) ($ipn['mc_fee']   ?? 0));
        $net       = round($gross - $fee, 2);

        if (! $txnId || ! $reference) {
            Log::channel('paypal')->warning('IPN missing txn_id or custom field', $ipn);
            return response('OK', 200);
        }

        // Step 5 — Deduplicate: ignore if txn_id already recorded
        if (Transaction::where('paypal_transaction_id', $txnId)->exists()) {
            Log::channel('paypal')->info('IPN duplicate — txn_id already processed', ['txn_id' => $txnId]);
            return response('OK', 200);
        }

        // Step 6 — Find the payment reference
        $ref = PaymentReference::where('reference_number', $reference)
            ->whereIn('status', ['active', 'paid'])
            ->first();

        if (! $ref) {
            Log::channel('paypal')->warning('IPN reference not found or invalid status', [
                'reference' => $reference,
                'txn_id'    => $txnId,
            ]);
            return response('OK', 200);
        }

        // Step 7 — Record the transaction
        $isEur     = $currency === 'EUR';
        $lkrRate   = ExchangeRate::getRate($currency);
        $finalLkr  = round($net * $lkrRate, 2);
        $payerName = trim(($ipn['first_name'] ?? '') . ' ' . ($ipn['last_name'] ?? ''))
            ?: ($ipn['payer_name'] ?? 'PayPal Customer');

        $transaction = Transaction::create([
            'user_id'               => $ref->user_id,
            'payment_reference_id'  => $ref->id,
            'payer_name'            => $payerName,
            'payer_email'           => $ipn['payer_email'] ?? null,
            'currency_type'         => $currency,
            'amount_usd'            => $isEur ? null  : $gross,
            'amount_eur'            => $isEur ? $gross : null,
            'fee_usd'               => $isEur ? 0     : $fee,
            'fee_eur'               => $isEur ? $fee  : 0,
            'final_usd'             => $isEur ? null  : $net,
            'final_eur'             => $isEur ? $net  : null,
            'final_lkr'             => $finalLkr,
            'lkr_rate'              => $lkrRate,
            'paypal_transaction_id' => $txnId,
            'status'                => 'cleared',
            'received_at'           => now()->toDateString(),
        ]);

        Log::channel('paypal')->info('IPN transaction recorded', [
            'transaction_id' => $transaction->id,
            'txn_id'         => $txnId,
            'reference'      => $reference,
            'net'            => $net,
            'currency'       => $currency,
            'lkr'            => $finalLkr,
        ]);

        // Step 8 — Mark reference as paid when the requested amount is met
        if ($ref->status === 'active') {
            $sumColumn = 'final_' . strtolower($currency);
            $totalPaid = $ref->transactions()->sum($sumColumn);

            if (! $ref->amount_requested || $totalPaid >= (float) $ref->amount_requested) {
                $ref->update(['status' => 'paid']);
                Log::channel('paypal')->info('Reference marked as paid', ['reference' => $reference]);
            }
        }

        return response('OK', 200);
    }

    /**
     * Post the raw IPN data back to PayPal with cmd=_notify-validate.
     * PayPal responds with "VERIFIED" or "INVALID".
     */
    private function verify(array $ipn): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(config('services.paypal.verify_url'), array_merge(
                    ['cmd' => '_notify-validate'],
                    $ipn
                ));

            $result = $response->body();

            Log::channel('paypal')->debug('IPN verify response', ['result' => $result]);

            return $response->successful() && $result === 'VERIFIED';
        } catch (\Exception $e) {
            Log::channel('paypal')->error('IPN verify HTTP exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
