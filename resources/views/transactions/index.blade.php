@extends('layouts.app')
@section('title', 'Transactions')

@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Pending (USD)</p>
        <p class="text-lg font-bold text-slate-800">${{ number_format($totals->pending_usd ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Pending (EUR)</p>
        <p class="text-lg font-bold text-slate-800">€{{ number_format($totals->pending_eur ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Pending (LKR)</p>
        <p class="text-lg font-bold text-slate-800">LKR {{ number_format($totals->pending_lkr ?? 0, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Released</p>
        <p class="text-lg font-bold text-emerald-600">LKR {{ number_format($totals->released_lkr ?? 0, 2) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">All statuses</option>
                <option value="cleared"  {{ request('status') === 'cleared'  ? 'selected' : '' }}>Cleared</option>
                <option value="released" {{ request('status') === 'released' ? 'selected' : '' }}>Released</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
            Filter
        </button>
        @if(request()->hasAny(['status','from','to']))
            <a href="{{ route('transactions.index') }}" class="text-sm text-slate-500 hover:text-slate-700 py-2">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    @if($transactions->isEmpty())
        <div class="text-center py-14">
            <p class="text-slate-500 text-sm">No transactions found</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Payer</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Reference</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Fees</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">LKR</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $tx->received_at->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-800 text-sm">{{ $tx->payer_name }}</p>
                                @if($tx->payer_email)
                                    <p class="text-xs text-slate-400">{{ $tx->payer_email }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 hidden sm:table-cell">
                                @if($tx->paymentReference)
                                    <a href="{{ route('references.show', $tx->paymentReference) }}"
                                       class="font-mono text-xs text-indigo-600 hover:underline">
                                        {{ $tx->paymentReference->reference_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $tx->display_amount }}</td>
                            <td class="px-5 py-3.5 hidden md:table-cell text-slate-500 text-xs">
                                @if($tx->fee_usd > 0) ${{ number_format($tx->fee_usd, 2) }}
                                @elseif($tx->fee_eur > 0) €{{ number_format($tx->fee_eur, 2) }}
                                @else —
                                @endif
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell text-slate-600 text-xs">
                                @if($tx->final_lkr) LKR {{ number_format($tx->final_lkr, 2) }}
                                @else —
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $tx->status_badge }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
