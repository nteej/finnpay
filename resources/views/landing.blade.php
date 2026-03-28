<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinnPay — Freelancer Payment Platform</title>
    <meta name="description" content="FinnPay helps freelancers receive international payments seamlessly. Generate payment references, let clients pay via PayPal, and get released to your bank account on a schedule you choose.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .float-slow { animation: floatY 6s ease-in-out infinite; }
        .float-slower { animation: floatY 8s ease-in-out infinite reverse; }
        @keyframes floatY {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .card-shine {
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.04) 50%, rgba(255,255,255,0.12) 100%);
        }
        .step-line::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 100%;
            width: 2px;
            height: 48px;
            background: linear-gradient(to bottom, #003580, transparent);
            transform: translateX(-50%);
        }
        .ticker-track {
            display: flex;
            align-items: center;
            width: max-content;
            animation: ticker 30s linear infinite;
        }
        .ticker-track:hover { animation-play-state: paused; }
        @keyframes ticker {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased">

{{-- ───────────────────── NAV ───────────────────── --}}
<header class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
        <a href="#" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#003580] flex items-center justify-center">
                <span class="text-white font-black text-xs tracking-tight">FP</span>
            </div>
            <span class="font-bold text-[#003580] text-lg tracking-tight">FinnPay</span>
        </a>

        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
            <a href="#how-it-works"    class="hover:text-[#003580] transition-colors">How it works</a>
            <a href="#features"        class="hover:text-[#003580] transition-colors">Features</a>
            <a href="#find-freelancers" class="hover:text-[#003580] transition-colors">Find Talent</a>
            <a href="#packages"        class="hover:text-[#003580] transition-colors">Packages</a>
            <a href="#faq"             class="hover:text-[#003580] transition-colors">FAQ</a>
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-slate-600 hover:text-[#003580] transition-colors hidden sm:block">
                Sign in
            </a>
            <a href="{{ route('register') }}"
               class="text-sm font-semibold bg-[#003580] hover:bg-[#002868] text-white px-4 py-2 rounded-lg transition-colors">
                Get started free
            </a>
        </div>
    </div>
</header>

{{-- ───────────────────── HERO ───────────────────── --}}
<section class="relative bg-[#003580] hero-grid overflow-hidden pt-16">
    {{-- Decorative blobs --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 -left-16 w-72 h-72 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 py-24 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Copy --}}
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-semibold text-blue-200 bg-white/10 px-3 py-1.5 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Built for Sri Lankan Freelancers
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[1.08] tracking-tight">
                    Get paid globally.<br>
                    <span class="text-[#A8C4E8]">Collect locally.</span>
                </h1>
                <p class="mt-6 text-blue-100 text-lg leading-relaxed max-w-lg">
                    FinnPay bridges the gap between international clients and your local bank. Generate a payment reference, share it — and we handle the rest.
                </p>

                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 bg-white text-[#003580] font-semibold px-6 py-3 rounded-xl hover:bg-blue-50 transition-colors text-sm">
                        Start collecting payments
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works"
                       class="inline-flex items-center gap-2 border border-white/25 text-white font-semibold px-6 py-3 rounded-xl hover:bg-white/10 transition-colors text-sm">
                        See how it works
                    </a>
                </div>

                <div class="mt-10 flex items-center gap-6">
                    <div class="flex -space-x-2">
                        @foreach(['D','R','A','S','K'] as $i => $l)
                        <div class="w-8 h-8 rounded-full border-2 border-[#003580] flex items-center justify-center text-xs font-bold text-white"
                             style="background:{{ ['#1e40af','#1d4ed8','#2563eb','#3b82f6','#60a5fa'][$i] }}">{{ $l }}</div>
                        @endforeach
                    </div>
                    <p class="text-sm text-blue-200">Join <strong class="text-white">50+</strong> freelancers already using FinnPay</p>
                </div>
            </div>

            {{-- Hero card mock --}}
            <div class="relative flex justify-center lg:justify-end float-slow">
                <div class="w-80 rounded-2xl overflow-hidden shadow-2xl border border-white/15 card-shine">
                    {{-- Card header --}}
                    <div class="bg-white/10 px-5 py-4 border-b border-white/10">
                        <p class="text-blue-200 text-xs font-medium uppercase tracking-wider mb-0.5">Payment Reference</p>
                        <p class="text-white font-mono font-bold text-lg tracking-widest">FP-2026-7X4K</p>
                    </div>
                    {{-- Card body --}}
                    <div class="bg-white/5 px-5 py-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-200">Client</span>
                            <span class="text-white font-medium">Acme Design Co.</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-200">Amount</span>
                            <span class="text-white font-semibold">€ 1,200.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-200">Status</span>
                            <span class="inline-flex items-center gap-1 text-emerald-300 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Received
                            </span>
                        </div>
                        <div class="pt-2 border-t border-white/10 flex justify-between text-sm">
                            <span class="text-blue-200">Next release</span>
                            <span class="text-white font-semibold">1 Apr 2026</span>
                        </div>
                    </div>
                    {{-- Card footer --}}
                    <div class="bg-emerald-500/20 border-t border-emerald-400/20 px-5 py-3">
                        <p class="text-emerald-300 text-xs font-medium">LKR 385,200.00 pending release</p>
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-xl px-4 py-3 float-slower">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-800">Payment Released</p>
                            <p class="text-xs text-slate-500">LKR 96,300 to ComBank</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave divider --}}
    <div class="h-16 bg-white" style="clip-path: ellipse(55% 100% at 50% 100%)"></div>
</section>

{{-- ───────────────────── TRUST BAR ───────────────────── --}}
<section class="bg-white py-10 border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-slate-400 mb-6">Trusted by freelancers across Sri Lanka</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            @foreach([
                ['LKR 5LKH+', 'Released to banks'],
                ['20+',   'Payments processed'],
                ['99.9%',    'Uptime reliability'],
                ['< 24 hrs', 'Support response'],
            ] as [$stat, $label])
            <div>
                <p class="text-3xl font-bold text-[#003580]">{{ $stat }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────────────── EXCHANGE RATE TICKER ───────────────────── --}}
<section class="bg-[#003580] border-t border-white/10 overflow-hidden">
    @php
        $tickerItems = [
            ['flag' => '🇺🇸', 'from' => 'USD', 'to' => '🇱🇰 LKR', 'rate' => number_format($usdRate, 2)],
            ['flag' => '🇪🇺', 'from' => 'EUR', 'to' => '🇱🇰 LKR', 'rate' => number_format($eurRate, 2)],
            ['flag' => '🇺🇸', 'from' => 'USD', 'to' => '🇪🇺 EUR', 'rate' => number_format($usdRate / $eurRate, 4)],
            ['flag' => '🇪🇺', 'from' => 'EUR', 'to' => '🇺🇸 USD', 'rate' => number_format($eurRate / $usdRate, 4)],
            ['flag' => '🇱🇰', 'from' => 'LKR', 'to' => '🇺🇸 USD', 'rate' => number_format(1 / $usdRate, 5)],
            ['flag' => '🇱🇰', 'from' => 'LKR', 'to' => '🇪🇺 EUR', 'rate' => number_format(1 / $eurRate, 5)],
        ];
    @endphp

    <div class="flex items-stretch">
        {{-- Fixed: Live rates label --}}
        <div class="flex-shrink-0 flex items-center gap-2 px-4 sm:px-6 py-2.5 border-r border-white/15 bg-[#002868]">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
            <span class="text-blue-200 text-xs font-semibold whitespace-nowrap hidden sm:inline">Live Rates</span>
            @if($updatedAt)
                <span class="text-white/30 text-xs hidden lg:inline">&mdash; {{ \Carbon\Carbon::parse($updatedAt)->diffForHumans() }}</span>
            @endif
        </div>

        {{-- Scrolling ticker --}}
        <div class="flex-1 overflow-hidden py-2.5 cursor-default" title="Hover to pause">
            <div class="ticker-track">
                @foreach(array_merge($tickerItems, $tickerItems) as $item)
                    <div class="inline-flex items-center gap-2 px-5">
                        <span class="text-base leading-none select-none">{{ $item['flag'] }}</span>
                        <span class="text-blue-300 text-xs font-medium tracking-wide">{{ $item['from'] }}&thinsp;/&thinsp;{{ $item['to'] }}</span>
                        <span class="text-white font-bold font-mono text-sm">{{ $item['rate'] }}</span>
                    </div>
                    <span class="text-white/20 text-xs select-none px-1">&#x2022;</span>
                @endforeach
            </div>
        </div>

        {{-- Fixed: Converter button --}}
        <div class="flex-shrink-0 flex items-center px-4 sm:px-6 border-l border-white/15 bg-[#002868]">
            <button onclick="document.getElementById('converterModal').classList.remove('hidden')"
                    class="flex items-center gap-1.5 text-xs font-semibold text-[#003580] bg-white hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors whitespace-nowrap">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Converter
            </button>
        </div>
    </div>
</section>

{{-- ───────────────────── EXCHANGE RATE CHART ───────────────────── --}}
<section id="rate-chart" class="bg-white py-16 lg:py-20 border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">Rate History</span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-bold text-slate-800">Currency Exchange Trends</h2>
                <p class="mt-1.5 text-slate-500 text-sm">Buy &amp; sell rates to LKR — sourced from FinnPay market data.</p>
            </div>

            {{-- Time range tabs --}}
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-start sm:self-auto" id="rangeTabGroup">
                @foreach(['30' => '1M', '90' => '3M', '180' => '6M', '365' => '1Y'] as $days => $label)
                    <button onclick="setRange({{ $days }})"
                            data-range="{{ $days }}"
                            class="range-tab px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $days === '90' ? 'bg-white text-[#003580] shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Currency toggle --}}
        <div class="flex items-center gap-3 mb-6">
            <button onclick="toggleDataset('usd')" id="toggleUsd"
                    class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border-2 border-[#003580] text-[#003580] bg-white transition-all hover:bg-[#003580] hover:text-white">
                <span class="w-2.5 h-2.5 rounded-full bg-[#003580] inline-block"></span>
                USD / LKR
            </button>
            <button onclick="toggleDataset('eur')" id="toggleEur"
                    class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border-2 border-amber-500 text-amber-600 bg-white transition-all hover:bg-amber-500 hover:text-white">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                EUR / LKR
            </button>
            <span class="text-xs text-slate-400 ml-1">Click to show/hide</span>
        </div>

        {{-- Chart card --}}
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 sm:p-6">
            <canvas id="rateChart" height="320"></canvas>
        </div>

        {{-- Stats row --}}
        @php
            $usdHistory = $rateHistory['USD'] ?? [];
            $eurHistory = $rateHistory['EUR'] ?? [];
            $last30Days = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');

            $usdRecent = array_filter($usdHistory, fn($d) => $d >= $last30Days, ARRAY_FILTER_USE_KEY);
            $eurRecent = array_filter($eurHistory, fn($d) => $d >= $last30Days, ARRAY_FILTER_USE_KEY);

            $usdBuys  = array_column($usdRecent, 'buy');
            $eurBuys  = array_column($eurRecent, 'buy');

            $usdHigh  = $usdBuys ? max($usdBuys) : null;
            $usdLow   = $usdBuys ? min($usdBuys) : null;
            $usdRange = ($usdHigh && $usdLow) ? round($usdHigh - $usdLow, 4) : null;

            $eurHigh  = $eurBuys ? max($eurBuys) : null;
            $eurLow   = $eurBuys ? min($eurBuys) : null;
            $eurRange = ($eurHigh && $eurLow) ? round($eurHigh - $eurLow, 4) : null;
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
            @foreach([
                ['label' => 'USD 30-day High',  'value' => $usdHigh  ? number_format($usdHigh, 2)  : '—', 'sub' => 'Buy rate to LKR', 'color' => 'text-emerald-600'],
                ['label' => 'USD 30-day Low',   'value' => $usdLow   ? number_format($usdLow, 2)   : '—', 'sub' => 'Buy rate to LKR', 'color' => 'text-red-500'],
                ['label' => 'EUR 30-day High',  'value' => $eurHigh  ? number_format($eurHigh, 2)  : '—', 'sub' => 'Buy rate to LKR', 'color' => 'text-emerald-600'],
                ['label' => 'EUR 30-day Low',   'value' => $eurLow   ? number_format($eurLow, 2)   : '—', 'sub' => 'Buy rate to LKR', 'color' => 'text-red-500'],
            ] as $stat)
                <div class="bg-white rounded-xl border border-slate-200 px-4 py-3">
                    <p class="text-xs text-slate-500 font-medium">{{ $stat['label'] }}</p>
                    <p class="text-xl font-bold {{ $stat['color'] }} mt-0.5 font-mono">{{ $stat['value'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $stat['sub'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
(function () {
    const RAW = {
        USD: @json(array_map(fn($d) => $d, $rateHistory['USD'] ?? [])),
        EUR: @json(array_map(fn($d) => $d, $rateHistory['EUR'] ?? [])),
    };

    let currentRange = 90;
    let usdVisible   = true;
    let eurVisible   = true;

    function sliceData(currency, days) {
        const entries = Object.entries(RAW[currency]);
        return entries.slice(-days);
    }

    function buildDatasets(days) {
        const usdSlice = sliceData('USD', days);
        const eurSlice = sliceData('EUR', days);

        // Use all date labels from both (merged + sorted)
        const allDates = [...new Set([...usdSlice.map(e => e[0]), ...eurSlice.map(e => e[0])])].sort();

        const usdByDate = Object.fromEntries(usdSlice);
        const eurByDate = Object.fromEntries(eurSlice);

        return {
            labels: allDates.map(d => {
                const dt = new Date(d);
                return dt.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: allDates.length > 90 ? '2-digit' : undefined });
            }),
            rawLabels: allDates,
            usdBuy:  allDates.map(d => usdByDate[d]?.buy  ?? null),
            usdSell: allDates.map(d => usdByDate[d]?.sell ?? null),
            eurBuy:  allDates.map(d => eurByDate[d]?.buy  ?? null),
            eurSell: allDates.map(d => eurByDate[d]?.sell ?? null),
        };
    }

    const ctx = document.getElementById('rateChart').getContext('2d');

    const baseDatasets = (data) => [
        {
            label: 'USD Buy',
            data: data.usdBuy,
            borderColor: '#003580',
            backgroundColor: 'rgba(0,53,128,0.08)',
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 5,
            tension: 0.3,
            fill: false,
            hidden: !usdVisible,
        },
        {
            label: 'USD Sell',
            data: data.usdSell,
            borderColor: '#003580',
            backgroundColor: 'rgba(0,53,128,0.06)',
            borderWidth: 1,
            borderDash: [4, 3],
            pointRadius: 0,
            pointHoverRadius: 4,
            tension: 0.3,
            fill: '-1',
            hidden: !usdVisible,
        },
        {
            label: 'EUR Buy',
            data: data.eurBuy,
            borderColor: '#d97706',
            backgroundColor: 'rgba(217,119,6,0.08)',
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 5,
            tension: 0.3,
            fill: false,
            hidden: !eurVisible,
        },
        {
            label: 'EUR Sell',
            data: data.eurSell,
            borderColor: '#d97706',
            backgroundColor: 'rgba(217,119,6,0.06)',
            borderWidth: 1,
            borderDash: [4, 3],
            pointRadius: 0,
            pointHoverRadius: 4,
            tension: 0.3,
            fill: '-1',
            hidden: !eurVisible,
        },
    ];

    let data = buildDatasets(currentRange);
    const chart = new Chart(ctx, {
        type: 'line',
        data: { labels: data.labels, datasets: baseDatasets(data) },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 2,
                        padding: 16,
                        font: { size: 11, family: 'ui-monospace, monospace' },
                        color: '#64748b',
                        usePointStyle: false,
                    },
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    titleFont: { size: 11 },
                    bodyFont: { size: 12, family: 'ui-monospace, monospace' },
                    callbacks: {
                        title: (items) => data.rawLabels[items[0].dataIndex] ?? items[0].label,
                        label: (item) => ` ${item.dataset.label}: ${item.parsed.y?.toFixed(4) ?? '—'} LKR`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10 },
                        maxTicksLimit: 8,
                        maxRotation: 0,
                    },
                    border: { display: false },
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10, family: 'ui-monospace, monospace' },
                        callback: (v) => v.toFixed(0) + ' LKR',
                    },
                    border: { display: false },
                },
            },
        },
    });

    window.setRange = function (days) {
        currentRange = days;
        document.querySelectorAll('.range-tab').forEach(btn => {
            const active = parseInt(btn.dataset.range) === days;
            btn.classList.toggle('bg-white', active);
            btn.classList.toggle('text-[#003580]', active);
            btn.classList.toggle('shadow-sm', active);
            btn.classList.toggle('text-slate-500', !active);
        });
        data = buildDatasets(days);
        chart.data.labels   = data.labels;
        chart.data.datasets = baseDatasets(data);
        chart.update('active');
    };

    window.toggleDataset = function (currency) {
        if (currency === 'usd') {
            usdVisible = !usdVisible;
            const btn = document.getElementById('toggleUsd');
            btn.classList.toggle('bg-[#003580]', usdVisible);
            btn.classList.toggle('text-white', usdVisible);
            btn.classList.toggle('text-[#003580]', !usdVisible);
            btn.classList.toggle('bg-white', !usdVisible);
        } else {
            eurVisible = !eurVisible;
            const btn = document.getElementById('toggleEur');
            btn.classList.toggle('bg-amber-500', eurVisible);
            btn.classList.toggle('text-white', eurVisible);
            btn.classList.toggle('text-amber-600', !eurVisible);
            btn.classList.toggle('bg-white', !eurVisible);
        }
        data = buildDatasets(currentRange);
        chart.data.datasets = baseDatasets(data);
        chart.update('active');
    };
})();
</script>

{{-- ───────────────────── HOW IT WORKS ───────────────────── --}}
<section id="how-it-works" class="bg-[#F0F6FF] py-20 lg:py-28">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="text-center mb-14">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">How it works</span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-slate-800">Simple. Transparent. Reliable.</h2>
            <p class="mt-3 text-slate-500 max-w-xl mx-auto">From generating a reference to money landing in your bank — four straightforward steps.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                [
                    'step' => '01',
                    'title' => 'Create a Reference',
                    'desc' => 'Generate a unique payment reference number for your client with the amount, currency, and description.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
                [
                    'step' => '02',
                    'title' => 'Share the Link',
                    'desc' => 'Send your client a secure payment link. They pay via PayPal — no account needed on their end.',
                    'icon' => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z',
                ],
                [
                    'step' => '03',
                    'title' => 'We Hold & Convert',
                    'desc' => 'FinnPay collects the payment, applies the live exchange rate, and holds it in your balance.',
                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                ],
                [
                    'step' => '04',
                    'title' => 'Released to Your Bank',
                    'desc' => 'On your release schedule (1st & 16th, or custom), LKR is transferred directly to your Sri Lankan bank account.',
                    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                ],
            ] as $i => $step)
            <div class="bg-white rounded-2xl p-6 border border-slate-200 relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#EEF4FF] flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#003580]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-slate-100">{{ $step['step'] }}</span>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                @if($i < 3)
                    <div class="hidden lg:block absolute top-10 -right-3 z-10">
                        <svg class="w-6 h-6 text-[#003580]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────────────── FEATURES ───────────────────── --}}
<section id="features" class="bg-white py-20 lg:py-28">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="text-center mb-14">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">Features</span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-slate-800">Everything a freelancer needs</h2>
            <p class="mt-3 text-slate-500 max-w-xl mx-auto">No complicated setup. No waiting weeks for approvals. Just clean tooling built for independent professionals.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'title' => 'Payment References',
                    'desc'  => 'Generate unique, trackable reference numbers per client or project. Share a direct payment link — they pay, you collect.',
                    'icon'  => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14',
                    'color' => 'bg-blue-50 text-[#003580]',
                ],
                [
                    'title' => 'Multi-Currency Support',
                    'desc'  => 'Accept payments in USD and EUR. We apply live exchange rates and credit your balance in LKR.',
                    'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color' => 'bg-emerald-50 text-emerald-700',
                ],
                [
                    'title' => 'Scheduled Releases',
                    'desc'  => 'Pick a release cycle that fits your cashflow. Payments are automatically batched and released to your bank on schedule.',
                    'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'color' => 'bg-violet-50 text-violet-700',
                ],
                [
                    'title' => 'Multiple Bank Accounts',
                    'desc'  => 'Add multiple Sri Lankan bank accounts across different currencies and set a default for automatic releases.',
                    'icon'  => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                    'color' => 'bg-amber-50 text-amber-700',
                ],
                [
                    'title' => 'Real-time Tracking',
                    'desc'  => 'See every payment the moment it lands. Full transaction history with payer details, amounts, and status.',
                    'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    'color' => 'bg-rose-50 text-rose-700',
                ],
                [
                    'title' => 'Client-Friendly Checkout',
                    'desc'  => 'Your clients see a clean, professional payment page branded to FinnPay — no login required, PayPal checkout in seconds.',
                    'icon'  => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
                    'color' => 'bg-sky-50 text-sky-700',
                ],
            ] as $f)
            <div class="group bg-white border border-slate-200 rounded-2xl p-6 hover:border-[#003580]/30 hover:shadow-lg hover:shadow-blue-50 transition-all">
                <div class="w-10 h-10 rounded-xl {{ $f['color'] }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────────────── TALENT DIRECTORY ───────────────────── --}}
<section id="find-freelancers" class="bg-white py-20 lg:py-28 border-t border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="text-center mb-14">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">Talent Directory</span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-slate-800">Hire verified Sri Lankan freelancers</h2>
            <p class="mt-3 text-slate-500 max-w-xl mx-auto">Every freelancer on FinnPay is identity-verified. Browse profiles, review work history, and connect directly.</p>
        </div>

        {{-- Published freelancer profile cards --}}
        @if($featuredFreelancers->isNotEmpty())
        @php $avatarColors = ['bg-[#003580]','bg-violet-600','bg-emerald-600','bg-rose-600','bg-indigo-600']; @endphp
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($featuredFreelancers as $fp)
            @php
                $skills = $fp->skillsArray();
                $color  = $avatarColors[$fp->id % count($avatarColors)];
                $availColor = match($fp->availability) {
                    'open'      => 'bg-emerald-100 text-emerald-700',
                    'part_time' => 'bg-amber-100 text-amber-700',
                    default     => 'bg-slate-100 text-slate-500',
                };
            @endphp
            <a href="{{ route('freelancers.show', $fp->publicSlug()) }}"
               class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-[#003580]/30 hover:shadow-lg hover:shadow-blue-50 transition-all block">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full {{ $color }} flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($fp->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ $fp->user->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $fp->title ?: 'Freelancer' }}</p>
                    </div>
                    <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full {{ $availColor }}">
                        {{ $fp->availabilityLabel() }}
                    </span>
                </div>
                @if($skills)
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach(array_slice($skills, 0, 3) as $skill)
                        <span class="text-xs bg-[#EEF4FF] text-[#003580] px-2.5 py-0.5 rounded-full font-medium">{{ $skill }}</span>
                    @endforeach
                    @if(count($skills) > 3)
                        <span class="text-xs bg-slate-100 text-slate-500 px-2.5 py-0.5 rounded-full">+{{ count($skills) - 3 }}</span>
                    @endif
                </div>
                @endif
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    @if($fp->hourly_rate)
                        <span class="text-sm font-bold text-slate-800">
                            {{ $fp->hourly_rate_currency === 'EUR' ? '€' : '$' }}{{ $fp->hourly_rate }}/hr
                        </span>
                    @else
                        <span class="text-xs text-slate-400">Rate on request</span>
                    @endif
                    <span class="text-xs text-[#003580] font-medium">View profile →</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center mb-12 py-10 bg-slate-50 rounded-2xl border border-slate-200">
            <p class="text-slate-500 text-sm">No published profiles yet — <a href="{{ route('register') }}" class="text-[#003580] font-medium hover:underline">be the first to publish yours</a>.</p>
        </div>
        @endif

        {{-- Dual CTA --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('freelancers.index') }}"
               class="inline-flex items-center gap-2 bg-[#003580] hover:bg-[#002868] text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                Browse all freelancers
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 border border-[#003580] text-[#003580] hover:bg-[#EEF4FF] font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                Publish your profile
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ───────────────────── PACKAGES ───────────────────── --}}
<section id="packages" class="bg-[#003580] py-20 lg:py-28 relative overflow-hidden">
    <div class="absolute inset-0 hero-grid pointer-events-none opacity-50"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-5 sm:px-8 relative">
        <div class="text-center mb-14">
            <span class="text-xs font-semibold uppercase tracking-widest text-blue-200 bg-white/10 px-3 py-1.5 rounded-full">Packages</span>
            <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-white">Choose your release cycle</h2>
            <p class="mt-3 text-blue-200 max-w-xl mx-auto">Pick the plan that matches your cashflow needs. You can upgrade after 3 months.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @php
                $packages = \App\Models\ReleasePackage::where('is_active', true)->orderBy('sort_order')->get();
                $highlights = [
                    'starter'  => ['color' => 'border-slate-300/30 bg-white/5',  'badge' => 'bg-white/10 text-blue-100'],
                    'standard' => ['color' => 'border-white/50 bg-white/10',      'badge' => 'bg-white text-[#003580]'],
                    'pro'      => ['color' => 'border-slate-300/30 bg-white/5',  'badge' => 'bg-white/10 text-blue-100'],
                ];
            @endphp

            @foreach($packages as $pkg)
            @php
                $h = $highlights[$pkg->slug] ?? $highlights['starter'];
                $isPopular = $pkg->slug === 'standard';
            @endphp
            <div class="relative rounded-2xl border {{ $h['color'] }} p-6 {{ $isPopular ? 'ring-2 ring-white/30' : '' }}">
                @if($isPopular)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="bg-white text-[#003580] text-xs font-bold px-3 py-1 rounded-full shadow">Most Popular</span>
                </div>
                @endif

                <div class="mb-5">
                    <span class="inline-block text-xs font-semibold {{ $h['badge'] }} px-2.5 py-1 rounded-full mb-3">{{ $pkg->name }}</span>
                    <p class="text-blue-100 text-sm leading-relaxed">{{ $pkg->description }}</p>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-300">Release schedule</span>
                        <span class="text-white font-medium">{{ $pkg->scheduleLabel() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-300">Minimum balance</span>
                        <span class="text-white font-medium">
                            {{ $pkg->minimum_balance_lkr > 0 ? 'LKR ' . number_format($pkg->minimum_balance_lkr) : 'No minimum' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-300">Manual release</span>
                        <span class="text-white font-medium">{{ $pkg->allow_manual_release ? 'Yes' : 'No' }}</span>
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="block text-center text-sm font-semibold py-2.5 rounded-xl transition-colors
                   {{ $isPopular ? 'bg-white text-[#003580] hover:bg-blue-50' : 'bg-white/10 text-white hover:bg-white/20 border border-white/20' }}">
                    Get started
                </a>
            </div>
            @endforeach
        </div>

        <p class="text-center text-blue-300 text-xs mt-8">All packages include real-time tracking, multi-currency support, and unlimited payment references.</p>
    </div>
</section>

{{-- ───────────────────── SECURITY ───────────────────── --}}
<section class="bg-white py-16 border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">Security</span>
                <h2 class="mt-4 text-3xl font-bold text-slate-800">Your money is safe with us</h2>
                <p class="mt-3 text-slate-500 leading-relaxed">FinnPay uses industry-standard encryption and secure payment processing. We never store card details.</p>
                <ul class="mt-6 space-y-3">
                    @foreach([
                        'PayPal-powered payment processing',
                        'SSL/TLS encryption on all connections',
                        'Admin-verified freelancer accounts',
                        'Full audit trail on every transaction',
                        'No card data stored on our servers',
                    ] as $point)
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <div class="w-5 h-5 rounded-full bg-[#EEF4FF] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-[#003580]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label' => 'Encrypted Storage'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Verified Accounts'],
                    ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'PayPal Protected'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'label' => 'Full Audit Log'],
                ] as $s)
                <div class="bg-[#F0F6FF] border border-blue-100 rounded-xl p-5 text-center">
                    <div class="w-10 h-10 bg-[#EEF4FF] rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-[#003580]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">{{ $s['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ───────────────────── FAQ ───────────────────── --}}
<section id="faq" class="bg-[#F0F6FF] py-20 lg:py-28">
    <div class="max-w-3xl mx-auto px-5 sm:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold uppercase tracking-widest text-[#003580] bg-[#DDEEFF] px-3 py-1.5 rounded-full">FAQ</span>
            <h2 class="mt-4 text-3xl font-bold text-slate-800">Common questions</h2>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @foreach([
                [
                    'q' => 'How does FinnPay work for my client?',
                    'a' => 'You share a unique payment link. Your client opens it, enters their name, email, and amount, then pays via PayPal. No FinnPay account required for the client — just a PayPal-supported payment method.',
                ],
                [
                    'q' => 'What currencies are supported?',
                    'a' => 'Clients can pay in USD or EUR. FinnPay converts received amounts to LKR using the live exchange rate and credits your LKR balance.',
                ],
                [
                    'q' => 'When do I receive money in my bank?',
                    'a' => 'That depends on your package. Starter releases once a month, Standard and Pro release twice a month (1st & 16th). Pro also allows manual release requests anytime.',
                ],
                [
                    'q' => 'Can I have multiple bank accounts?',
                    'a' => 'Yes. You can add multiple Sri Lankan bank accounts — different banks, different currencies — and mark one as your default for automatic releases.',
                ],
                [
                    'q' => 'How long am I locked into a package?',
                    'a' => 'Each package has a 3-month lock period from activation. After that, you\'re free to switch packages anytime. Admin can override this lock in special circumstances.',
                ],
                [
                    'q' => 'What fees does FinnPay charge?',
                    'a' => 'A PayPal processing fee of approximately 4.9% is deducted from each payment. FinnPay itself does not charge additional platform fees on your package.',
                ],
            ] as $idx => $faq)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden"
                 x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 text-left">
                    <span class="font-medium text-slate-800 text-sm pr-4">{{ $faq['q'] }}</span>
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-5 pb-4 text-sm text-slate-500 leading-relaxed border-t border-slate-100 pt-3">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────────────── CTA ───────────────────── --}}
<section class="bg-[#003580] py-20">
    <div class="max-w-2xl mx-auto px-5 sm:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white">Ready to get paid?</h2>
        <p class="mt-4 text-blue-200 text-lg">Join hundreds of Sri Lankan freelancers collecting international payments the smart way.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-8">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-white text-[#003580] font-semibold px-7 py-3 rounded-xl hover:bg-blue-50 transition-colors text-sm">
                Create free account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 border border-white/25 text-white font-semibold px-7 py-3 rounded-xl hover:bg-white/10 transition-colors text-sm">
                Sign in
            </a>
        </div>
        <p class="text-blue-300 text-xs mt-6">No credit card required &middot; Verified within 24 hours</p>
    </div>
</section>

{{-- ───────────────────── FOOTER ───────────────────── --}}
<footer class="bg-[#002868] py-10">
    <div class="max-w-6xl mx-auto px-5 sm:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center">
                    <span class="text-white font-black text-xs tracking-tight">FP</span>
                </div>
                <span class="text-white font-semibold">FinnPay</span>
            </div>
            <nav class="flex flex-wrap justify-center gap-5 text-xs text-blue-300">
                <a href="#how-it-works" class="hover:text-white transition-colors">How it works</a>
                <a href="#features"     class="hover:text-white transition-colors">Features</a>
                <a href="#packages"     class="hover:text-white transition-colors">Packages</a>
                <a href="#faq"          class="hover:text-white transition-colors">FAQ</a>
                <a href="{{ route('login') }}"    class="hover:text-white transition-colors">Sign in</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a>
            </nav>
            <p class="text-blue-400 text-xs">&copy; {{ date('Y') }} FinnPay</p>
        </div>
    </div>
</footer>

{{-- ───────────────────── CURRENCY CONVERTER MODAL ───────────────────── --}}
<div id="converterModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
         x-data="converter()" x-init="init()">

        {{-- Header --}}
        <div class="bg-[#003580] px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-semibold text-base">Currency Converter</p>
                    <p class="text-blue-200 text-xs mt-0.5">Live rates from FinnPay</p>
                </div>
                <button onclick="document.getElementById('converterModal').classList.add('hidden')"
                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            {{-- Rate pills --}}
            <div class="flex gap-2 mt-4">
                <span class="bg-white/10 text-blue-100 text-xs font-medium px-2.5 py-1 rounded-full">
                    1 USD = {{ number_format($usdRate, 2) }} LKR
                </span>
                <span class="bg-white/10 text-blue-100 text-xs font-medium px-2.5 py-1 rounded-full">
                    1 EUR = {{ number_format($eurRate, 2) }} LKR
                </span>
            </div>
        </div>

        {{-- Converter body --}}
        <div class="p-6 space-y-4">

            {{-- Amount input --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Amount</label>
                <input type="number" x-model="amount" @input="convert()"
                       min="0" step="any" placeholder="0.00"
                       class="w-full border border-slate-300 rounded-xl px-4 py-3 text-lg font-semibold text-slate-800
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            {{-- From / To selectors --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">From</label>
                    <select x-model="from" @change="convert()"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800
                                   focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                        <option value="USD">🇺🇸 USD</option>
                        <option value="EUR">🇪🇺 EUR</option>
                        <option value="LKR">🇱🇰 LKR</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">To</label>
                    <select x-model="to" @change="convert()"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800
                                   focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                        <option value="LKR">🇱🇰 LKR</option>
                        <option value="USD">🇺🇸 USD</option>
                        <option value="EUR">🇪🇺 EUR</option>
                    </select>
                </div>
            </div>

            {{-- Swap button --}}
            <div class="flex justify-center">
                <button @click="swap()" title="Swap currencies"
                        class="w-9 h-9 rounded-full border-2 border-slate-200 hover:border-[#003580] hover:bg-[#EEF4FF]
                               flex items-center justify-center transition-all group">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-[#003580] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </button>
            </div>

            {{-- Result --}}
            <div class="bg-[#EEF4FF] border border-blue-100 rounded-xl px-5 py-4">
                <p class="text-xs text-[#003580] font-medium mb-1">Converted Amount</p>
                <p class="text-3xl font-bold text-[#003580] font-mono" x-text="result || '—'"></p>
                <p class="text-xs text-slate-500 mt-1.5" x-show="result">
                    <span x-text="amount || '0'"></span> <span x-text="from"></span>
                    = <span x-text="result"></span> <span x-text="to"></span>
                </p>
            </div>

            {{-- Note --}}
            <p class="text-xs text-slate-400 text-center">
                Indicative rates only. PayPal processing fee (~4.9%) applies on actual payments.
            </p>
        </div>
    </div>
</div>

{{-- Alpine.js for FAQ accordion + currency converter --}}
<script>
    const FP_RATES = {
        USD: { LKR: {{ $usdRate }}, EUR: {{ round($usdRate / $eurRate, 6) }}, USD: 1 },
        EUR: { LKR: {{ $eurRate }}, USD: {{ round($eurRate / $usdRate, 6) }}, EUR: 1 },
        LKR: { USD: {{ round(1 / $usdRate, 8) }}, EUR: {{ round(1 / $eurRate, 8) }}, LKR: 1 },
    };

    function converter() {
        return {
            amount: '',
            from:   'USD',
            to:     'LKR',
            result: '',
            init() { this.convert(); },
            convert() {
                const amt = parseFloat(this.amount);
                if (!amt || isNaN(amt)) { this.result = ''; return; }
                const rate = FP_RATES[this.from]?.[this.to] ?? 1;
                const val  = amt * rate;
                this.result = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(val);
            },
            swap() {
                [this.from, this.to] = [this.to, this.from];
                this.convert();
            },
        };
    }
</script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>

</body>
</html>
