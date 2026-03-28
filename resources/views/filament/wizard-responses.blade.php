@php
    $responses = $wizard->responses->load('question');

    // ── Build sort_order → response map (skip orphaned responses) ────────────
    $responseMap = $responses->filter(fn($r) => $r->question !== null)
                             ->keyBy('question.sort_order');

    // ── Compliance flag rules: [sort_order, trigger_contains, severity, label, description] ──
    $complianceRules = [
        [12, 'Yes',                    'high',   'PEP Declared',
            'Applicant is or has been a Politically Exposed Person — enhanced due diligence is required before approval.'],
        [13, 'Yes',                    'high',   'Financial Sanctions Active',
            'Subject to financial sanctions, court orders, or asset freezes — onboarding must not proceed without legal review.'],
        [15, 'No',                     'high',   'Income Legitimacy Unconfirmed',
            'Applicant has not confirmed that income is derived entirely from legitimate, service-based work.'],
        [30, 'No',                     'high',   'Verification Consent Refused',
            'Applicant has not consented to identity verification — onboarding cannot proceed under Finnish KYC regulations.'],
        [31, 'No',                     'high',   'Declaration Not Accepted',
            'Applicant has not signed the accuracy declaration — required under Finnish Act 444/2017 (AML).'],
        [14, 'Yes',                    'medium', 'Previous Account Closure',
            'A financial institution previously closed their account for compliance or AML-related reasons.'],
        [17, 'Yes',                    'medium', 'Unresolved Payment Disputes',
            'Reports unresolved payment disputes or chargebacks in the past 12 months.'],
        [21, 'Cryptocurrency',         'medium', 'Cryptocurrency Payment Method',
            'Uses cryptocurrency as a primary payment method — additional AML monitoring required per FIN-FSA guidance.'],
        [28, 'Yes',                    'medium', 'High Client Concentration Risk',
            'A single client represents more than 50% of income — financial stability risk noted.'],
        [10, 'No — I have not filed',  'medium', 'Tax Returns Not Filed',
            'Has not filed income tax returns for freelance earnings — potential tax compliance issue under Finnish law.'],
    ];

    // ── Evaluate flags ────────────────────────────────────────────────────────
    $flags = [];
    $flaggedSortOrders = [];
    foreach ($complianceRules as [$sortOrder, $trigger, $severity, $label, $desc]) {
        $r = $responseMap->get($sortOrder);
        $answer = $r?->answer ?? '';
        if (str_contains($answer, $trigger)) {
            $flags[] = ['severity' => $severity, 'label' => $label, 'desc' => $desc, 'answer' => $answer, 'sort_order' => $sortOrder];
            $flaggedSortOrders[] = $sortOrder;
        }
    }
    $highFlags   = array_filter($flags, fn($f) => $f['severity'] === 'high');
    $mediumFlags = array_filter($flags, fn($f) => $f['severity'] === 'medium');
    $riskLevel   = count($highFlags) > 0 ? 'high' : (count($mediumFlags) > 0 ? 'medium' : 'low');

    // ── Income profile ────────────────────────────────────────────────────────
    $monthlyLabel  = $responseMap->get(18)?->answer ?? null;
    $annualLabel   = $responseMap->get(19)?->answer ?? null;
    $currencyLabel = $responseMap->get(20)?->answer ?? null;

    $monthlyEurMap = [
        'Under €500'         => 250,
        '€500 – €1,500'      => 1000,
        '€1,500 – €3,000'    => 2250,
        '€3,000 – €6,000'    => 4500,
        '€6,000 – €10,000'   => 8000,
        'Over €10,000'       => 12000,
    ];
    $monthlyEur = $monthlyEurMap[$monthlyLabel] ?? null;
    $monthlyLkr = $monthlyEur ? $monthlyEur * 330 : null;

    $suggestedPackage = null;
    if ($monthlyEur !== null) {
        if ($monthlyEur < 500)        $suggestedPackage = ['name' => 'Below Starter minimum', 'color' => 'amber', 'note' => 'May not meet minimum balance requirement'];
        elseif ($monthlyEur < 1500)   $suggestedPackage = ['name' => 'Starter',   'color' => 'slate',  'note' => '1× per month, min LKR 15,000'];
        elseif ($monthlyEur < 6000)   $suggestedPackage = ['name' => 'Standard',  'color' => 'indigo', 'note' => '2× per month, min LKR 50,000'];
        else                          $suggestedPackage = ['name' => 'Pro',        'color' => 'amber',  'note' => 'Weekly releases, min LKR 500,000'];
    }

    // ── Sensitive sort_orders (always highlighted) ────────────────────────────
    $sensitiveOrders = [12, 13, 14, 15, 16, 17, 30, 31];

    // ── Grouped by section ────────────────────────────────────────────────────
    $bySection = $responses->groupBy('question.section');
@endphp

<div class="space-y-5 text-sm">

    {{-- ── Header bar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            {{-- Risk badge --}}
            @if($riskLevel === 'high')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 ring-1 ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    HIGH RISK
                </span>
            @elseif($riskLevel === 'medium')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-300">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    REVIEW REQUIRED
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 ring-1 ring-green-300">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    LOW RISK
                </span>
            @endif
            {{-- Flag counts --}}
            @if(count($highFlags) > 0)
                <span class="text-xs font-semibold text-red-600">{{ count($highFlags) }} critical flag{{ count($highFlags) !== 1 ? 's' : '' }}</span>
            @endif
            @if(count($mediumFlags) > 0)
                <span class="text-xs font-semibold text-amber-600">{{ count($mediumFlags) }} review item{{ count($mediumFlags) !== 1 ? 's' : '' }}</span>
            @endif
        </div>
        <div class="text-xs text-gray-400 text-right">
            <span>Submitted {{ $wizard->completed_at->format('d M Y, H:i') }}</span>
            <span class="mx-1">&middot;</span>
            <span>{{ $responses->count() }} answer{{ $responses->count() !== 1 ? 's' : '' }}</span>
        </div>
    </div>

    {{-- ── Compliance Flags Panel ── --}}
    @if(count($flags) > 0)
        <div class="rounded-lg border {{ count($highFlags) > 0 ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }} p-4 space-y-3">
            <p class="text-xs font-bold uppercase tracking-widest {{ count($highFlags) > 0 ? 'text-red-600' : 'text-amber-600' }} mb-1">
                Compliance Flags — Action Required
            </p>
            @foreach($flags as $flag)
                <div class="flex items-start gap-3 py-2 {{ !$loop->last ? 'border-b ' . (count($highFlags) > 0 ? 'border-red-200' : 'border-amber-200') : '' }}">
                    @if($flag['severity'] === 'high')
                        <div class="mt-0.5 w-5 h-5 flex-shrink-0 rounded-full bg-red-200 flex items-center justify-center">
                            <svg class="w-3 h-3 text-red-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </div>
                    @else
                        <div class="mt-0.5 w-5 h-5 flex-shrink-0 rounded-full bg-amber-200 flex items-center justify-center">
                            <svg class="w-3 h-3 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold {{ $flag['severity'] === 'high' ? 'text-red-800' : 'text-amber-800' }} text-xs">{{ $flag['label'] }}</p>
                        <p class="text-xs {{ $flag['severity'] === 'high' ? 'text-red-700' : 'text-amber-700' }} mt-0.5 leading-relaxed">{{ $flag['desc'] }}</p>
                        <p class="text-xs mt-1 font-mono {{ $flag['severity'] === 'high' ? 'text-red-600 bg-red-100' : 'text-amber-700 bg-amber-100' }} inline-block px-2 py-0.5 rounded">
                            Answered: "{{ $flag['answer'] }}"
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Income Profile Card ── --}}
    @if($monthlyEur || $annualLabel || $currencyLabel)
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-3">Income Profile</p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                @if($monthlyEur)
                    <div>
                        <p class="text-xs text-blue-500 mb-0.5">Est. Monthly Volume</p>
                        <p class="font-bold text-blue-900">€{{ number_format($monthlyEur) }}<span class="text-xs font-normal text-blue-600 ml-1">/ month</span></p>
                        @if($monthlyLkr)
                            <p class="text-xs text-blue-500 mt-0.5">≈ LKR {{ number_format($monthlyLkr) }} at current rate</p>
                        @endif
                    </div>
                @endif
                @if($annualLabel)
                    <div>
                        <p class="text-xs text-blue-500 mb-0.5">Last Full Year</p>
                        <p class="font-semibold text-blue-900">{{ $annualLabel }}</p>
                    </div>
                @endif
                @if($currencyLabel)
                    <div class="col-span-2">
                        <p class="text-xs text-blue-500 mb-1">Payment Currencies</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(', ', $currencyLabel) as $ccy)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-200 text-blue-800">{{ trim($ccy) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($suggestedPackage)
                    <div class="col-span-2 pt-2 border-t border-blue-200">
                        <p class="text-xs text-blue-500 mb-1">Suggested Package</p>
                        @if($suggestedPackage['color'] === 'amber')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 ring-1 ring-amber-300">
                                ⚠ {{ $suggestedPackage['name'] }}
                            </span>
                        @elseif($suggestedPackage['color'] === 'indigo')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 ring-1 ring-indigo-300">
                                {{ $suggestedPackage['name'] }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 ring-1 ring-slate-300">
                                {{ $suggestedPackage['name'] }}
                            </span>
                        @endif
                        <span class="text-xs text-blue-500 ml-2">{{ $suggestedPackage['note'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Sectioned Answers ── --}}
    @if($responses->isEmpty())
        <p class="text-sm text-gray-400 text-center py-6">No responses recorded.</p>
    @else
        @foreach($bySection as $section => $sectionResponses)
            @php
                $sectionSortOrders = $sectionResponses->pluck('question.sort_order')->toArray();
                $sectionHasHigh    = count(array_intersect($sectionSortOrders, array_column(array_filter($flags, fn($f) => $f['severity'] === 'high'), 'sort_order'))) > 0;
                $sectionHasMedium  = count(array_intersect($sectionSortOrders, array_column(array_filter($flags, fn($f) => $f['severity'] === 'medium'), 'sort_order'))) > 0;
            @endphp

            <div>
                {{-- Section header --}}
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $section }}</p>
                    @if($sectionHasHigh)
                        <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">flagged</span>
                    @elseif($sectionHasMedium)
                        <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">review</span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">✓ clear</span>
                    @endif
                </div>

                <div class="space-y-2">
                    @foreach($sectionResponses->sortBy('question.sort_order') as $response)
                        @if(!$response->question) @continue @endif
                        @php
                            $so        = $response->question->sort_order;
                            $isFlagged = in_array($so, $flaggedSortOrders);
                            $isHighFlag = in_array($so, array_column(array_filter($flags, fn($f) => $f['severity'] === 'high'), 'sort_order'));
                            $isSensitive = in_array($so, $sensitiveOrders);
                            $qType     = $response->question->type;
                            $answer    = $response->answer ?? '';

                            if ($isHighFlag) {
                                $cardClass = 'bg-red-50 border border-red-200';
                                $qClass    = 'text-red-600';
                                $aClass    = 'text-red-900';
                            } elseif ($isFlagged) {
                                $cardClass = 'bg-amber-50 border border-amber-200';
                                $qClass    = 'text-amber-700';
                                $aClass    = 'text-amber-900';
                            } elseif ($isSensitive) {
                                $cardClass = 'bg-gray-50 border border-gray-300';
                                $qClass    = 'text-gray-600';
                                $aClass    = 'text-gray-900';
                            } else {
                                $cardClass = 'bg-gray-50 border border-gray-100';
                                $qClass    = 'text-gray-500';
                                $aClass    = 'text-gray-800';
                            }
                        @endphp

                        <div class="rounded-lg px-4 py-3 {{ $cardClass }}">
                            <p class="text-xs font-semibold {{ $qClass }} mb-1.5 leading-snug">
                                {{ $response->question->question_text }}
                                @if($response->question->is_required)
                                    <span class="font-normal opacity-60 ml-1">*</span>
                                @endif
                            </p>

                            @if(! $answer)
                                <p class="text-xs text-gray-400 italic">Not answered</p>
                            @elseif($qType === 'boolean')
                                @if($answer === 'Yes')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold
                                        {{ $isSensitive || $isFlagged ? 'bg-red-200 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Yes
                                    </span>
                                @elseif($answer === 'No')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold
                                        {{ $isFlagged ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-700' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        No
                                    </span>
                                @else
                                    <p class="text-sm {{ $aClass }}">{{ $answer }}</p>
                                @endif
                            @elseif(str_contains($answer, ', '))
                                {{-- Multi-value (checkbox) --}}
                                <div class="flex flex-wrap gap-1 mt-0.5">
                                    @foreach(explode(', ', $answer) as $val)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-300 text-gray-700">{{ trim($val) }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm {{ $aClass }} leading-relaxed">{{ $answer }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
