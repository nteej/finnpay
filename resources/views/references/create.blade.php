@extends('layouts.app')
@section('title', 'New Payment Reference')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('references.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to References
    </a>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-1">Create Payment Reference</h2>
        <p class="text-sm text-slate-500 mb-6">Share the generated reference with your client so they can make a payment.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('references.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Project / Work Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                       placeholder="e.g. Website Design — Client ABC">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                <textarea name="notes" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent resize-none"
                          placeholder="Any additional details for this payment...">{{ old('notes') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Expected Amount <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="number" name="amount_requested" value="{{ old('amount_requested') }}" step="0.01" min="0"
                           class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                  focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                    <select name="currency"
                            class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                   focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry Date <span class="text-slate-400 font-normal">(optional)</span></label>
                <input type="date" name="expires_at" value="{{ old('expires_at') }}" min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#003580] hover:bg-[#002868] text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                    Generate Reference
                </button>
                <a href="{{ route('references.index') }}"
                   class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
