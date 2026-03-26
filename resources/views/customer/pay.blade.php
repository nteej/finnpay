<!DOCTYPE html>
<html lang="en" class="h-full bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment — FinnPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex items-start justify-center p-4 py-10">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-xl">FinnPay</span>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500 text-white rounded-2xl p-6 mb-4 text-center">
                <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-semibold text-lg mb-1">Payment Successful!</p>
                <p class="text-emerald-100 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-indigo-600 px-6 py-5">
                <p class="text-indigo-200 text-xs font-medium uppercase tracking-wider mb-1">Payment Reference</p>
                <p class="text-white font-mono font-bold text-xl tracking-widest">{{ $ref->reference_number }}</p>
            </div>

            {{-- Reference Details --}}
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $ref->title }}</p>
                        @if($ref->notes)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $ref->notes }}</p>
                        @endif
                    </div>
                    @if($ref->amount_requested)
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="text-xs text-slate-500">Amount due</p>
                            <p class="text-sm font-bold text-indigo-700">
                                {{ $ref->currency === 'EUR' ? '€' : '$' }}{{ number_format($ref->amount_requested, 2) }}
                            </p>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    <p class="text-xs text-slate-500">Freelancer: <strong class="text-slate-700">{{ $ref->user->name }}</strong></p>
                </div>
            </div>

            {{-- Payment Form --}}
            <form method="POST" action="{{ route('customer.pay.submit', $ref->reference_number) }}" class="p-6 space-y-4">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Your Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="payer_name" value="{{ old('payer_name') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Enter your full name">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Your Email <span class="text-red-500">*</span></label>
                    <input type="email" name="payer_email" value="{{ old('payer_email') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="your@email.com">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $ref->amount_requested) }}"
                               required step="0.01" min="1"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                        <select name="currency"
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                            <option value="USD" {{ ($ref->currency === 'USD') ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($ref->currency === 'EUR') ? 'selected' : '' }}>EUR (€)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                    <strong>Note:</strong> A PayPal processing fee (~4.9%) will be deducted from your payment. The net amount will be credited to the freelancer.
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg
                               transition-colors text-sm mt-2">
                    Confirm Payment
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">
            Secured by FinnPay &middot; Freelancer Payment Platform
        </p>
    </div>
</body>
</html>
