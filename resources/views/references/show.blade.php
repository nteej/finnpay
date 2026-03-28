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
        <div class="bg-[#EEF4FF] border border-blue-200 rounded-xl p-5 mb-5">
            <p class="text-xs font-medium text-[#003580] uppercase tracking-wider mb-2">Payment Reference Number</p>
            <div class="flex items-center gap-3">
                <span id="refCode" class="text-2xl font-mono font-bold text-[#003580] tracking-widest">{{ $reference->reference_number }}</span>
                <button onclick="copyRef()" class="p-2 rounded-lg hover:bg-[#DDEEFF] text-[#003580] transition-colors" title="Copy">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
            <p id="copyMsg" class="text-xs text-[#003580] mt-1 hidden">Copied to clipboard!</p>
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

        {{-- PayPal Payment Link --}}
        @php $paypalUrl = $reference->paypalUrl(); @endphp
        <div class="rounded-xl border-2 border-[#009CDE] bg-[#F0F8FF] p-4 mb-5">
            <div class="flex items-center gap-2 mb-3">
                {{-- PayPal wordmark --}}
                <svg class="h-5" viewBox="0 0 101 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.237 2.43C11.12 1.163 9.107.594 6.53.594H.672a.895.895 0 0 0-.885.756L.001 30.526a.537.537 0 0 0 .531.614h3.9l.98-6.228-.03.197a.893.893 0 0 1 .882-.756h1.837c3.607 0 6.432-1.466 7.257-5.706.025-.126.046-.249.065-.37-.104-.055-.104-.055 0 0 .245-1.562-.001-2.624-.744-3.586l-.442-.26z" fill="#003087"/>
                    <path d="M13.678 18.028c-.837 4.24-3.65 5.706-7.257 5.706H4.584a.895.895 0 0 0-.882.756l-1.24 7.866a.536.536 0 0 0 .53.614h3.553c.39 0 .722-.285.783-.672l.032-.167.62-3.935.04-.218a.793.793 0 0 1 .783-.672h.493c3.195 0 5.7-1.298 6.432-5.051.305-1.566.148-2.874-.66-3.793a3.149 3.149 0 0 0-.9-.434l.51.0z" fill="#009CDE"/>
                    <path d="M12.87 17.706a6.577 6.577 0 0 0-.808-.179 10.23 10.23 0 0 0-1.625-.12H6.15a.793.793 0 0 0-.782.672L4.47 23.93l-.03.197a.893.893 0 0 1 .882-.756h1.837c3.607 0 6.432-1.466 7.257-5.706.02-.12.04-.244.065-.37a4.44 4.44 0 0 0-.831-.168l.22-.12z" fill="#012169"/>
                    <path d="M39.17 8.407h-5.74a.895.895 0 0 0-.884.756l-2.317 14.682a.537.537 0 0 0 .53.614h2.739a.626.626 0 0 0 .619-.529l.657-4.166a.895.895 0 0 1 .884-.757h1.815c3.78 0 5.96-1.829 6.53-5.457.257-1.586.01-2.833-.73-3.707-.813-.963-2.254-1.436-4.103-1.436zm.663 5.377c-.313 2.057-1.884 2.057-3.404 2.057h-.865l.607-3.843a.536.536 0 0 1 .53-.454h.396c1.034 0 2.01 0 2.514.59.301.353.393.877.222 1.65zM56.14 13.725h-2.75a.536.536 0 0 0-.53.453l-.136.862-.216-.314c-.669-.97-2.161-1.294-3.65-1.294-3.415 0-6.332 2.587-6.899 6.215-.295 1.811.124 3.54 1.148 4.748.94 1.109 2.284 1.571 3.883 1.571 2.752 0 4.28-1.769 4.28-1.769l-.138.857a.537.537 0 0 0 .531.614h2.477a.895.895 0 0 0 .884-.757l1.487-9.416a.536.536 0 0 0-.531-.77h.16zm-3.838 6.015c-.298 1.765-1.699 2.95-3.484 2.95-.896 0-1.613-.288-2.073-.833-.457-.541-.629-1.311-.485-2.168.279-1.749 1.7-2.972 3.458-2.972.877 0 1.59.291 2.058.841.47.554.657 1.329.526 2.182zM72.023 13.725h-2.762a.896.896 0 0 0-.74.392l-4.271 6.287-1.811-6.042a.895.895 0 0 0-.858-.637h-2.714a.537.537 0 0 0-.508.714l3.41 10.007-3.207 4.527a.537.537 0 0 0 .438.85h2.759a.893.893 0 0 0 .737-.385l10.303-14.875a.537.537 0 0 0-.436-.838h-.34z" fill="#003087"/>
                    <path d="M81.034 8.407h-5.74a.895.895 0 0 0-.884.756L72.093 23.845a.537.537 0 0 0 .531.614h2.941a.626.626 0 0 0 .619-.529l.656-4.166a.895.895 0 0 1 .884-.757h1.816c3.78 0 5.96-1.829 6.53-5.457.256-1.586.01-2.833-.73-3.707-.814-.963-2.255-1.436-4.106-1.436zm.663 5.377c-.313 2.057-1.884 2.057-3.404 2.057h-.864l.607-3.843a.536.536 0 0 1 .53-.454h.396c1.034 0 2.01 0 2.513.59.302.353.393.877.222 1.65zM97.999 13.725h-2.75a.536.536 0 0 0-.53.453l-.136.862-.216-.314c-.668-.97-2.16-1.294-3.65-1.294-3.415 0-6.332 2.587-6.899 6.215-.295 1.811.124 3.54 1.148 4.748.94 1.109 2.285 1.571 3.884 1.571 2.752 0 4.28-1.769 4.28-1.769l-.138.857a.537.537 0 0 0 .531.614h2.477a.895.895 0 0 0 .884-.757l1.487-9.416a.536.536 0 0 0-.531-.77h.153zm-3.838 6.015c-.298 1.765-1.699 2.95-3.484 2.95-.896 0-1.613-.288-2.073-.833-.457-.541-.629-1.311-.485-2.168.28-1.749 1.7-2.972 3.458-2.972.877 0 1.59.291 2.058.841.47.554.657 1.329.527 2.182zM101.246 8.776l-2.35 14.946a.538.538 0 0 0 .531.614h2.372c.39 0 .722-.285.784-.671l2.317-14.684a.537.537 0 0 0-.531-.614h-2.592a.538.538 0 0 0-.531.409z" fill="#009CDE"/>
                </svg>
                <span class="text-xs font-semibold text-[#003087]">Direct Payment Link</span>
                @if(!$reference->amount_requested)
                    <span class="ml-auto text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">No amount set — customer enters amount</span>
                @endif
            </div>

            {{-- URL display + copy --}}
            <div class="flex items-center gap-2 bg-white border border-[#009CDE]/40 rounded-lg px-3 py-2.5 mb-3">
                <input id="paypalLink" type="text" readonly value="{{ $paypalUrl }}"
                       class="flex-1 text-xs text-slate-600 bg-transparent border-none outline-none font-mono truncate">
                <button onclick="copyPaypal()" id="copyPaypalBtn"
                        class="flex-shrink-0 flex items-center gap-1 text-xs font-semibold text-[#003087] hover:text-[#009CDE] transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy
                </button>
            </div>

            {{-- Reference ID tag + open button --}}
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-1.5 text-xs text-[#003087]">
                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    <span>Internal ref: <code class="font-mono font-semibold">{{ $reference->reference_number }}</code> is embedded as <code class="font-mono">custom</code> parameter for settlement tracking</span>
                </div>
                <a href="{{ $paypalUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold bg-[#009CDE] hover:bg-[#003087] text-white px-3 py-1.5 rounded-lg transition-colors">
                    Open in PayPal
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Actions --}}
        @if($reference->status === 'active')
            <div class="flex items-center gap-3 pt-1">
                <a href="{{ route('references.edit', $reference) }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-[#003580] hover:text-[#002868] border border-[#003580]/30 hover:border-[#003580] px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('references.destroy', $reference) }}"
                      onsubmit="return confirm('Delete this reference? The PayPal link will stop working.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
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
function copyPaypal() {
    const url = document.getElementById('paypalLink').value;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('copyPaypalBtn');
        btn.textContent = 'Copied!';
        btn.classList.add('text-emerald-600');
        setTimeout(() => { btn.innerHTML = `<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>Copy`; btn.classList.remove('text-emerald-600'); }, 2000);
    });
}
</script>
@endsection
