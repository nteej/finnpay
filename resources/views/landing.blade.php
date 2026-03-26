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
            <a href="#how-it-works" class="hover:text-[#003580] transition-colors">How it works</a>
            <a href="#features"     class="hover:text-[#003580] transition-colors">Features</a>
            <a href="#packages"     class="hover:text-[#003580] transition-colors">Packages</a>
            <a href="#faq"          class="hover:text-[#003580] transition-colors">FAQ</a>
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
                ['LKR 48M+', 'Released to banks'],
                ['2,800+',   'Payments processed'],
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

{{-- Alpine.js for FAQ accordion --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>

</body>
</html>
