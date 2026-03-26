@extends('layouts.app')
@section('title', 'Reference: ' . $reference->reference_number)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('references.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to References
    </a>

    {{-- Reference Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-slate-800">{{ $reference->title }}</h2>
                @if($reference->notes)
                    <p class="text-sm text-slate-500 mt-1">{{ $reference->notes }}</p>
                @endif
            </div>
            @php
                $badge = match($reference->status) {
                    'active'    => 'bg-green-100 text-green-700',
                    'paid'      => 'bg-blue-100 text-blue-700',
                    'expired'   => 'bg-slate-100 text-slate-600',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default     => 'bg-slate-100 text-slate-600',
                };
            @endphp
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                {{ ucfirst($reference->status) }}
            </span>
        </div>

        {{-- Reference Number Display --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 mb-5">
            <p class="text-xs font-medium text-indigo-600 uppercase tracking-wider mb-2">Payment Reference Number</p>
            <div class="flex items-center gap-3">
                <span id="refCode" class="text-2xl font-mono font-bold text-indigo-800 tracking-widest">{{ $reference->reference_number }}</span>
                <button onclick="copyRef()" class="p-2 rounded-lg hover:bg-indigo-100 text-indigo-600 transition-colors" title="Copy">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
            <p id="copyMsg" class="text-xs text-indigo-500 mt-1 hidden">Copied to clipboard!</p>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Expected Amount</p>
                <p class="text-sm font-semibold text-slate-800">
                    @if($reference->amount_requested)
                        {{ $reference->currency === 'EUR' ? '€' : '$' }}{{ number_format($reference->amount_requested, 2) }}
                    @else
                        <span class="text-slate-400 font-normal">Not specified</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Currency</p>
                <p class="text-sm font-semibold text-slate-800">{{ $reference->currency }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Payments Received</p>
                <p class="text-sm font-semibold text-slate-800">{{ $reference->transactions->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Created</p>
                <p class="text-sm font-semibold text-slate-800">{{ $reference->created_at->format('d M Y') }}</p>
            </div>
        </div>

        {{-- Payment Link --}}
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-5">
            <p class="text-xs font-medium text-slate-600 mb-2">Customer Payment Link</p>
            <div class="flex items-center gap-2">
                <input id="payLink" type="text" readonly
                       value="{{ route('customer.pay', $reference->reference_number) }}"
                       class="flex-1 text-xs text-slate-600 bg-transparent border-none outline-none font-mono truncate">
                <button onclick="copyLink()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">Copy link</button>
            </div>
        </div>

        {{-- Actions --}}
        @if($reference->status === 'active')
            <form method="POST" action="{{ route('references.destroy', $reference) }}"
                  onsubmit="return confirm('Cancel this payment reference?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                    Cancel reference
                </button>
            </form>
        @endif
    </div>

    {{-- Transactions --}}
    @if($reference->transactions->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Payments Received</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($reference->transactions as $tx)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($tx->payer_name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800">{{ $tx->payer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $tx->payer_email }} &middot; {{ $tx->received_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">{{ $tx->display_amount }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $tx->status_badge }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
function copyRef() {
    navigator.clipboard.writeText('{{ $reference->reference_number }}').then(() => {
        document.getElementById('copyMsg').classList.remove('hidden');
        setTimeout(() => document.getElementById('copyMsg').classList.add('hidden'), 2000);
    });
}
function copyLink() {
    navigator.clipboard.writeText(document.getElementById('payLink').value);
}
</script>
@endsection
