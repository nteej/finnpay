@extends('layouts.app')
@section('title', 'Claim: ' . $release->release_code)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('releases.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Claims
    </a>

    {{-- Rejection notice --}}
    @if($release->status === 'rejected')
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">Claim rejected</p>
                <p class="text-xs text-red-700 mt-0.5">{{ $release->rejection_reason }}</p>
                <p class="text-xs text-red-500 mt-1">Your cleared transactions have been returned to your pending balance and you may raise a new claim.</p>
            </div>
        </div>
    @elseif($release->status === 'pending_approval')
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Awaiting admin approval</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    This claim was submitted {{ $release->claimed_at->diffForHumans() }}.
                    Once approved, funds will be deposited on the next scheduled release date.
                </p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-base font-semibold text-slate-800 font-mono">{{ $release->release_code }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $release->period_start->format('d M Y') }} – {{ $release->period_end->format('d M Y') }}
                </p>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $release->status_badge }}">
                @if($release->status === 'pending_approval') Pending Approval
                @elseif($release->status === 'rejected') Rejected
                @else {{ ucfirst($release->status) }}
                @endif
            </span>
        </div>

        {{-- Amount Highlight --}}
        <div class="{{ $release->status === 'completed' ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} border rounded-xl p-5 mb-5">
            <p class="text-xs font-medium {{ $release->status === 'completed' ? 'text-emerald-600' : 'text-slate-500' }} uppercase tracking-wider mb-1">
                {{ $release->status === 'completed' ? 'Total Released' : 'Claim Amount' }}
            </p>
            <p class="text-3xl font-bold {{ $release->status === 'completed' ? 'text-emerald-800' : 'text-slate-800' }}">
                LKR {{ number_format($release->total_lkr, 2) }}
            </p>
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
                <p class="text-xs text-slate-500 mb-0.5">
                    {{ $release->status === 'pending_approval' ? 'Claimed At' : 'Processed At' }}
                </p>
                <p class="font-semibold text-slate-800">
                    @if($release->status === 'pending_approval')
                        {{ $release->claimed_at?->format('d M Y, H:i') ?? '—' }}
                    @else
                        {{ $release->processed_at?->format('d M Y, H:i') ?? '—' }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Bank Details --}}
        @if($release->bank_name)
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <p class="text-xs font-medium text-slate-600 uppercase tracking-wider mb-2">
                    {{ $release->status === 'completed' ? 'Sent To' : 'To Be Sent To' }}
                </p>
                <p class="text-sm font-semibold text-slate-800">{{ $release->bank_account_holder }}</p>
                <p class="text-xs text-slate-500">{{ $release->bank_name }} &middot; Account: {{ $release->bank_account }}</p>
            </div>
        @endif
    </div>

    {{-- Transactions in this claim --}}
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
