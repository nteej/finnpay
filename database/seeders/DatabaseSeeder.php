<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\ExchangeRate;
use App\Models\PaymentReference;
use App\Models\ReleaseCycleSetting;
use App\Models\ReleasePackage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ──────────────────────────────────────────────────────
        $admin = User::create([
            'name'          => 'FinnPay Admin',
            'email'         => 'admin@finnpay.test',
            'password'      => Hash::make('admin123'),
            'freelancer_id' => null,
            'is_admin'      => true,
            'is_verified'   => true,
            'verified_at'   => now(),
            'is_active'     => true,
        ]);

        // ── Exchange rates ───────────────────────────────────────────────────
        ExchangeRate::insert([
            [
                'currency_from' => 'USD',
                'currency_to'   => 'LKR',
                'buy_rate'      => 295.0000,
                'sell_rate'     => 302.0000,
                'is_active'     => true,
                'updated_by'    => $admin->id,
                'notes'         => 'Initial rate — March 2026',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'currency_from' => 'EUR',
                'currency_to'   => 'LKR',
                'buy_rate'      => 330.0000,
                'sell_rate'     => 341.0000,
                'is_active'     => true,
                'updated_by'    => $admin->id,
                'notes'         => 'Initial rate — March 2026',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // ── Release Packages ─────────────────────────────────────────────────
        $pkgStarter = ReleasePackage::create([
            'name'                 => 'Starter',
            'slug'                 => 'starter',
            'description'          => 'One release per month on the 16th. Best for freelancers with moderate income.',
            'color'                => 'slate',
            'releases_per_month'   => 1,
            'release_day_1'        => 16,
            'release_day_2'        => null,
            'minimum_balance_lkr'  => 15000,
            'allow_manual_release' => false,
            'sort_order'           => 1,
        ]);

        $pkgStandard = ReleasePackage::create([
            'name'                 => 'Standard',
            'slug'                 => 'standard',
            'description'          => 'Two releases per month (1st & 16th) with manual release option.',
            'color'                => 'indigo',
            'releases_per_month'   => 2,
            'release_day_1'        => 1,
            'release_day_2'        => 16,
            'minimum_balance_lkr'  => 50000,
            'allow_manual_release' => true,
            'sort_order'           => 2,
        ]);

        ReleasePackage::create([
            'name'                 => 'Pro',
            'slug'                 => 'pro',
            'description'          => 'One releases per week with priority processing.',
            'color'                => 'amber',
            'releases_per_month'   => 4,
            'release_day_1'        => 1,
            'release_day_2'        => 16,
            'minimum_balance_lkr'  => 500000,
            'allow_manual_release' => true,
            'sort_order'           => 3,
        ]);

        // ── Release cycle settings (global fallback) ─────────────────────────
        ReleaseCycleSetting::create([
            'release_day_1'        => 1,
            'release_day_2'        => 16,
            'allow_manual_release' => true,
            'minimum_balance_lkr'  => 0,
            'notes'                => 'Default: 1st and 16th of every month. Manual release allowed.',
        ]);

        // ── Demo freelancer (User-0001) ──────────────────────────────────────
        $user = User::create([
            'name'           => 'Alex Rivera',
            'email'          => 'user-0001@finnpay.test',
            'password'       => Hash::make('password'),
            'freelancer_id'  => 'FPL-000001',
            'phone'          => '+94 77 100 0001',
            'local_currency' => 'LKR',
            'is_verified'    => true,
            'verified_at'    => now(),
            'verified_by'    => $admin->id,
            'is_active'      => true,
        ]);

        $user->userPackages()->create([
            'release_package_id' => $pkgStandard->id,
            'started_at'         => now(),
            'locked_until'       => now()->addMonths(3),
            'is_active'          => true,
            'changed_by'         => $admin->id,
        ]);

        BankAccount::create([
            'user_id'             => $user->id,
            'bank_name'           => 'Commercial Bank of Ceylon',
            'bank_branch'         => 'Colombo Main Branch',
            'bank_account_number' => '8001234567',
            'bank_account_holder' => 'Alex Rivera',
            'currency'            => 'LKR',
            'is_default'          => true,
        ]);

        // ── Payment References ───────────────────────────────────────────────
        $refs = [
            ['NoCopyrightSounds Limited', 'Music licensing — NCS March batch', 'USD'],
            ['Zero to Infinity',          'Content sync license — EU market',  'EUR'],
            ['CBMG LLC',                  'Royalty payment — CBMG March',      'USD'],
            ['Cresta La Cultura',         'Music usage rights — EU sync',      'EUR'],
            ['ENTITY MUSIC',              'Copyright management service',       'USD'],
            ['INTERTONE LLC',             'Track licensing fee',                'USD'],
            ['Andrei Shapkin',            'Composer royalty — EU',              'EUR'],
        ];

        $refModels = [];
        foreach ($refs as [$payer, $title, $currency]) {
            $ref = PaymentReference::create([
                'user_id'          => $user->id,
                'reference_number' => PaymentReference::generateReference(),
                'title'            => $title,
                'currency'         => $currency,
                'status'           => 'paid',
            ]);
            $refModels[$payer] = $ref;
        }

        // ── Transactions from CSV (User-0001, March 2026) ────────────────────
        $transactions = [
            ['NoCopyrightSounds Limited', 'USD', 160.00, 7.80,  0,    152.20, null,  null,  null,  0.8346, 307.00, '2026-03-09'],
            ['Zero to Infinity',          'EUR', null,   0,     37.11, null,  2.35,  null,  34.76, null,   307.00, '2026-03-10'],
            ['CBMG LLC',                  'USD', 300.00, 0,     0,    300.00, null,  null,  null,  0.8379, 307.00, '2026-03-13']
        ];

        foreach ($transactions as [$refKey, $ccy, $amtUsd, $feeUsd, $amtEur, $finalUsd, $feeEur, $amtEurRaw, $finalEur, $cvRate, $lkrRate, $date]) {
            $final    = $ccy === 'USD' ? $finalUsd : $finalEur;
            $finalLkr = round((float)$final * $lkrRate, 2);

            Transaction::create([
                'user_id'              => $user->id,
                'payment_reference_id' => isset($refModels[$refKey]) ? $refModels[$refKey]->id : null,
                'payer_name'           => $refKey === 'ENTITY MUSIC'
                    ? 'ENTITY MUSIC AUTHORS COPYRIGHTS MANAGEMENT SERVICES - FZCO' : $refKey,
                'currency_type'        => $ccy,
                'amount_usd'           => $amtUsd,
                'fee_usd'              => $feeUsd,
                'final_usd'            => $finalUsd,
                'amount_eur'           => $amtEur > 0 ? $amtEur : null,
                'fee_eur'              => $feeEur ?? 0,
                'final_eur'            => $finalEur,
                'cv_rate'              => $cvRate,
                'lkr_rate'             => $lkrRate,
                'final_lkr'            => $finalLkr,
                'status'               => 'cleared',
                'received_at'          => $date,
            ]);
        }
    }
}
