<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Release;
use App\Models\ReleaseCycleSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReleaseController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $releases = $user->releases()
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        $cycle          = $user->cycleSettings();
        $nextRelease    = $cycle->nextReleaseDate();
        $pendingCount   = $user->transactions()->where('status', 'cleared')->count();
        $pendingBalance = $user->pendingBalance();
        $activePackage  = $user->activeUserPackage();

        return view('releases.index', compact('releases', 'nextRelease', 'pendingCount', 'pendingBalance', 'cycle', 'activePackage'));
    }

    public function show(Release $release)
    {
        $this->authorize('view', $release);
        $release->load('transactions');
        return view('releases.show', compact('release'));
    }

    public function process(Request $request)
    {
        $user  = Auth::user();
        $cycle = $user->cycleSettings();

        if (! $cycle->allow_manual_release) {
            return back()->with('error', 'Manual releases are currently disabled. Payments will be released on the scheduled dates.');
        }

        $bankAccount = $user->defaultBankAccount();

        if (! $bankAccount) {
            return back()->with('error', 'Please add a bank account in your profile before requesting a release.');
        }

        $cleared = $user->transactions()->where('status', 'cleared')->get();

        if ($cleared->isEmpty()) {
            return back()->with('error', 'No cleared transactions available to release.');
        }

        $usdRate = ExchangeRate::getRate('USD');
        $eurRate = ExchangeRate::getRate('EUR');

        $totalLkr = ($cleared->whereNotNull('final_usd')->sum('final_usd') * $usdRate)
                  + ($cleared->whereNotNull('final_eur')->sum('final_eur') * $eurRate);

        if ($cycle->minimum_balance_lkr > 0 && $totalLkr < $cycle->minimum_balance_lkr) {
            return back()->with('error', 'Minimum balance of LKR ' . number_format($cycle->minimum_balance_lkr) . ' required for release. Current balance: LKR ' . number_format($totalLkr, 2));
        }

        DB::transaction(function () use ($user, $cleared, $usdRate, $eurRate, $totalLkr, $bankAccount) {
            $totalUsd = $cleared->whereNotNull('final_usd')->sum('final_usd');
            $totalEur = $cleared->whereNotNull('final_eur')->sum('final_eur');

            $release = Release::create([
                'user_id'               => $user->id,
                'release_code'          => Release::generateCode($user->id),
                'period_start'          => $cleared->min('received_at'),
                'period_end'            => $cleared->max('received_at'),
                'transaction_count'     => $cleared->count(),
                'total_usd'             => $totalUsd,
                'total_eur'             => $totalEur,
                'total_lkr'             => round($totalLkr, 2),
                'exchange_rate_usd_lkr' => $usdRate,
                'exchange_rate_eur_lkr' => $eurRate,
                'bank_name'             => $bankAccount->bank_name,
                'bank_account'          => $bankAccount->bank_account_number,
                'bank_account_holder'   => $bankAccount->bank_account_holder,
                'status'                => 'completed',
                'scheduled_at'          => now(),
                'processed_at'          => now(),
            ]);

            Transaction::whereIn('id', $cleared->pluck('id'))->update([
                'status'     => 'released',
                'release_id' => $release->id,
            ]);
        });

        return redirect()->route('releases.index')
            ->with('success', 'Payment released successfully to your bank account.');
    }
}
