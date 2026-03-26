@extends('layouts.app')
@section('title', 'Release: ' . $release->release_code)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('releases.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Releases
    </a>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-base font-semibold text-slate-800 font-mono">{{ $release->release_code }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $release->period_start->format('d M Y') }} – {{ $release->period_end->format('d M Y') }}
                </p>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $release->status_badge }}">
                {{ ucfirst($release->status) }}
            </span>
        </div>

        {{-- Amount Highlight --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-5">
            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-1">Total Released</p>
            <p class="text-3xl font-bold text-emerald-800">LKR {{ number_format($release->total_lkr, 2) }}</p>
        </div>

        <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm mb-5">
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Total (USD)</p>
                <p class="font-semibold text-slate-800">${{ number_format($release->total_usd, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Total (EUR)</p>
                <p class="font-semibold text-slate-800">€{{ number_format($release->total_eur, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">USD → LKR Rate</p>
                <p class="font-semibold text-slate-800">{{ $release->exchange_rate_usd_lkr }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">EUR → LKR Rate</p>
                <p class="font-semibold text-slate-800">{{ $release->exchange_rate_eur_lkr }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Transactions</p>
                <p class="font-semibold text-slate-800">{{ $release->transaction_count }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Processed At</p>
                <p class="font-semibold text-slate-800">{{ $release->processed_at?->format('d M Y, H:i') ?? '—' }}</p>
            </div>
        </div>

        {{-- Bank Details --}}
        @if($release->bank_name)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <p class="text-xs font-medium text-slate-600 uppercase tracking-wider mb-2">Sent To</p>
                <p class="text-sm font-semibold text-slate-800">{{ $release->bank_account_holder }}</p>
                <p class="text-xs text-slate-500">{{ $release->bank_name }} &middot; Account: {{ $release->bank_account }}</p>
            </div>
        @endif
    </div>

    {{-- Transactions in this release --}}
    @if($release->transactions->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Included Transactions</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($release->transactions as $tx)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($tx->payer_name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $tx->payer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $tx->received_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $tx->display_amount }}</p>
                            <p class="text-xs text-slate-400">LKR {{ number_format($tx->final_lkr, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
