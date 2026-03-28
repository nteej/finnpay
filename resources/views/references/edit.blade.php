@extends('layouts.app')
@section('title', 'Edit Reference: ' . $reference->reference_number)

@section('content')
<div class="max-w-xl">
    <a href="{{ route('references.show', $reference) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Reference
    </a>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Edit Payment Reference</h2>
                <p class="text-sm text-slate-500 mt-0.5 font-mono">{{ $reference->reference_number }}</p>
            </div>
            <span class="text-xs font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">Active</span>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('references.update', $reference) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Project / Work Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $reference->title) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                       placeholder="e.g. Website Design — Client ABC">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                <textarea name="notes" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent resize-none"
                          placeholder="Any additional details for this payment...">{{ old('notes', $reference->notes) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Expected Amount <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="number" name="amount_requested" value="{{ old('amount_requested', $reference->amount_requested) }}" step="0.01" min="0"
                           class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                  focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                    <select name="currency"
                            class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                                   focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                        <option value="USD" {{ old('currency', $reference->currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency', $reference->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry Date <span class="text-slate-400 font-normal">(optional)</span></label>
                <input type="date" name="expires_at"
                       value="{{ old('expires_at', $reference->expires_at?->format('Y-m-d')) }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#003580] hover:bg-[#002868] text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                    Save Changes
                </button>
                <a href="{{ route('references.show', $reference) }}"
                   class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="mt-4 bg-white rounded-xl border border-red-100 p-5">
        <h3 class="text-sm font-semibold text-slate-700 mb-1">Delete Reference</h3>
        <p class="text-xs text-slate-500 mb-3">This will permanently cancel the reference. The PayPal link will stop working.</p>
        <form method="POST" action="{{ route('references.destroy', $reference) }}"
              onsubmit="return confirm('Delete this reference? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-sm font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 px-4 py-2 rounded-lg transition-colors">
                Delete reference
            </button>
        </form>
    </div>
</div>
@endsection
