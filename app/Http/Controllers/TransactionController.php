<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->transactions()->with('paymentReference');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('received_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('received_at', '<=', $request->to);
        }

        $transactions = $query->orderByDesc('received_at')->paginate(20)->withQueryString();

        $totals = Auth::user()->transactions()
            ->selectRaw('
                SUM(CASE WHEN status="cleared" THEN final_usd ELSE 0 END) as pending_usd,
                SUM(CASE WHEN status="cleared" THEN final_eur ELSE 0 END) as pending_eur,
                SUM(CASE WHEN status="cleared" THEN final_lkr ELSE 0 END) as pending_lkr,
                SUM(CASE WHEN status="released" THEN final_lkr ELSE 0 END) as released_lkr
            ')
            ->first();

        return view('transactions.index', compact('transactions', 'totals'));
    }
}
