<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pendingBalance    = $user->pendingBalance();
        $recentTransactions = $user->transactions()
            ->with('paymentReference')
            ->orderByDesc('received_at')
            ->limit(5)
            ->get();

        $totalReceived = $user->transactions()
            ->whereIn('status', ['cleared', 'released'])
            ->sum('final_eur');

        $totalReleased = $user->releases()
            ->where('status', 'completed')
            ->sum('total_lkr');

        $lastRelease    = $user->releases()->where('status', 'completed')->latest('processed_at')->first();
        $pendingCount   = $user->transactions()->where('status', 'cleared')->count();
        $activeRefs     = $user->paymentReferences()->where('status', 'active')->count();
        $activePackage  = $user->activeUserPackage();
        $nextRelease    = $user->cycleSettings()->nextReleaseDate();

        return view('dashboard.index', compact(
            'user', 'pendingBalance', 'recentTransactions',
            'totalReceived', 'totalReleased', 'lastRelease',
            'nextRelease', 'pendingCount', 'activeRefs', 'activePackage'
        ));
    }
}
