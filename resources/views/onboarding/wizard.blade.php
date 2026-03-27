<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F0F6FF]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding — FinnPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col antialiased">

{{-- Header --}}
<header class="bg-[#003580] px-5 py-4">
    <div class="max-w-2xl mx-auto flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center">
            <span class="text-[#003580] font-black text-xs tracking-tight">FP</span>
        </div>
        <span class="text-white font-bold text-lg tracking-tight">FinnPay</span>
        <span class="text-blue-300 text-sm ml-2">Freelancer Onboarding</span>
    </div>
</header>

<div class="flex-1 max-w-2xl w-full mx-auto px-5 py-8"
     x-data="wizard({{ $sections->count() }})"
     x-init="init()">

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    {{-- Progress --}}
    <div class="mb-8">
        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
            <span>Step <span x-text="currentStep + 1"></span> of {{ $sections->count() }}</span>
            <span x-text="Math.round(((currentStep + 1) / {{ $sections->count() }}) * 100) + '%'"></span>
        </div>
        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-[#003580] rounded-full transition-all duration-500"
                 :style="'width: ' + Math.round(((currentStep + 1) / {{ $sections->count() }}) * 100) + '%'"></div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('onboarding.submit', $wizard->token) }}">
        @csrf

        @php $stepIndex = 0; @endphp
        @foreach($sections as $sectionName => $questions)
        <div x-show="currentStep === {{ $stepIndex }}" x-cloak>
            <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
                <div class="mb-6">
                    <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#EEF4FF] px-2.5 py-1 rounded-full">
                        {{ $sectionName }}
                    </span>
                </div>

                <div class="space-y-5">
                    @foreach($questions as $q)
                    @php $existingAnswer = $existing->get($q->id)?->answer; @endphp
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            {{ $q->question_text }}
                            @if($q->is_required)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                        @if($q->helper_text)
                            <p class="text-xs text-slate-400 mb-2">{{ $q->helper_text }}</p>
                        @endif

                        @switch($q->type)
                            @case('textarea')
                                <textarea name="answers[{{ $q->id }}]"
                                          rows="3"
                                          {{ $q->is_required ? 'required' : '' }}
                                          class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent resize-none">{{ old('answers.'.$q->id, $existingAnswer) }}</textarea>
                                @break
                            @case('select')
                                <select name="answers[{{ $q->id }}]"
                                        {{ $q->is_required ? 'required' : '' }}
                                        class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                                    <option value="">Select an option…</option>
                                    @foreach($q->options ?? [] as $opt)
                                        <option value="{{ $opt }}" @selected(old('answers.'.$q->id, $existingAnswer) === $opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @break
                            @case('radio')
                                <div class="space-y-2">
                                    @foreach($q->options ?? [] as $opt)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
                                               {{ $q->is_required ? 'required' : '' }}
                                               @checked(old('answers.'.$q->id, $existingAnswer) === $opt)
                                               class="w-4 h-4 text-[#003580] border-slate-300 focus:ring-[#003580]">
                                        <span class="text-sm text-slate-700">{{ $opt }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @break
                            @case('checkbox')
                                @php $checked = $existingAnswer ? explode(', ', $existingAnswer) : []; @endphp
                                <div class="space-y-2">
                                    @foreach($q->options ?? [] as $opt)
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}"
                                               @checked(in_array($opt, $checked))
                                               class="w-4 h-4 rounded text-[#003580] border-slate-300 focus:ring-[#003580]">
                                        <span class="text-sm text-slate-700">{{ $opt }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @break
                            @case('boolean')
                                <div class="flex gap-4">
                                    @foreach(['Yes', 'No'] as $opt)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
                                               {{ $q->is_required ? 'required' : '' }}
                                               @checked(old('answers.'.$q->id, $existingAnswer) === $opt)
                                               class="w-4 h-4 text-[#003580] border-slate-300 focus:ring-[#003580]">
                                        <span class="text-sm text-slate-700">{{ $opt }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @break
                            @case('number')
                                <input type="number" name="answers[{{ $q->id }}]"
                                       value="{{ old('answers.'.$q->id, $existingAnswer) }}"
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                                @break
                            @case('date')
                                <input type="date" name="answers[{{ $q->id }}]"
                                       value="{{ old('answers.'.$q->id, $existingAnswer) }}"
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                                @break
                            @default
                                <input type="text" name="answers[{{ $q->id }}]"
                                       value="{{ old('answers.'.$q->id, $existingAnswer) }}"
                                       {{ $q->is_required ? 'required' : '' }}
                                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                        @endswitch
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @php $stepIndex++; @endphp
        @endforeach

        {{-- Navigation --}}
        <div class="flex items-center justify-between">
            <button type="button" @click="prevStep()"
                    x-show="currentStep > 0"
                    class="flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
            </button>
            <div x-show="currentStep === 0" class="w-1"></div>

            <button type="button" @click="nextStep()"
                    x-show="currentStep < totalSteps - 1"
                    class="flex items-center gap-2 bg-[#003580] hover:bg-[#002868] text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button type="submit"
                    x-show="currentStep === totalSteps - 1"
                    class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Submit answers
            </button>
        </div>
    </form>
</div>

<footer class="text-center text-xs text-slate-400 py-6 border-t border-slate-200 bg-white">
    &copy; {{ date('Y') }} FinnPay &mdash; Your responses are securely stored.
</footer>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak]{display:none!important}</style>
<script>
function wizard(totalSteps) {
    return {
        currentStep: 0,
        totalSteps,
        init() {
            // If validation failed, jump to the step that has errors
            @if($errors->any())
            const errorFields = document.querySelectorAll('[name]');
            // Stay on step 0 if no specific indicator
            @endif
        },
        nextStep() { if (this.currentStep < this.totalSteps - 1) this.currentStep++; window.scrollTo(0,0); },
        prevStep() { if (this.currentStep > 0) this.currentStep--; window.scrollTo(0,0); },
    };
}
</script>
</body>
</html>
