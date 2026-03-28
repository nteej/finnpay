<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $csv  = public_path('exchanges.csv');
        $rows = array_filter(array_map('trim', file($csv)));
        array_shift($rows); // skip header

        // Parse all rows grouped by currency
        $byCurrency = [];
        foreach ($rows as $row) {
            [$currency, $date, $buyRate, $sellRate] = str_getcsv($row, ',', '"', '');
            $byCurrency[$currency][$date] = [
                'buy_rate'  => round((float) $buyRate, 4),
                'sell_rate' => round((float) $sellRate, 4),
            ];
        }

        // Find the latest date per currency (to mark as active)
        $latestDates = array_map(fn($dates) => max(array_keys($dates)), $byCurrency);

        $now  = now();
        $rows = [];

        foreach ($byCurrency as $currency => $dates) {
            foreach ($dates as $date => $rates) {
                $isLatest = ($date === $latestDates[$currency]);
                $rows[] = [
                    'currency_from' => $currency,
                    'currency_to'   => 'LKR',
                    'rate_date'     => $date,
                    'buy_rate'      => $rates['buy_rate'],
                    'sell_rate'     => $rates['sell_rate'],
                    'is_active'     => $isLatest,
                    'updated_by'    => null,
                    'notes'         => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        // Upsert — safe to re-run
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('exchange_rates')->upsert(
                $chunk,
                ['currency_from', 'currency_to', 'rate_date'],
                ['buy_rate', 'sell_rate', 'is_active', 'updated_at']
            );
        }

        // Deactivate all non-latest rows (including legacy null-date rows)
        ExchangeRate::whereNull('rate_date')->update(['is_active' => false]);
        foreach ($latestDates as $currency => $latestDate) {
            ExchangeRate::where('currency_from', $currency)
                ->where('currency_to', 'LKR')
                ->where('rate_date', '!=', $latestDate)
                ->update(['is_active' => false]);
        }

        $this->command->info('Imported ' . array_sum(array_map('count', $byCurrency)) . ' exchange rate records.');
    }
}
