<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — FinnPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F0F6FF] text-slate-800 antialiased min-h-screen flex flex-col">

{{-- Nav --}}
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#003580] flex items-center justify-center">
                <span class="text-white font-black text-xs tracking-tight">FP</span>
            </div>
            <span class="font-bold text-[#003580] text-lg tracking-tight">FinnPay</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('freelancers.index') }}"
               class="text-sm font-medium text-slate-600 hover:text-[#003580] transition-colors hidden sm:block">
                Find Talent
            </a>
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-slate-600 hover:text-[#003580] transition-colors hidden sm:block">
                Sign in
            </a>
            <a href="{{ route('register') }}"
               class="text-sm font-semibold bg-[#003580] hover:bg-[#002868] text-white px-4 py-2 rounded-lg transition-colors">
                Get started
            </a>
        </div>
    </div>
</header>

{{-- Content --}}
<main class="flex-1 max-w-6xl w-full mx-auto px-5 sm:px-8 py-10">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-[#002868] py-8 mt-10">
    <div class="max-w-6xl mx-auto px-5 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-white/10 flex items-center justify-center">
                <span class="text-white font-black text-[9px] tracking-tight">FP</span>
            </div>
            <span class="text-white font-semibold text-sm">FinnPay</span>
        </div>
        <p class="text-blue-400 text-xs">&copy; {{ date('Y') }} FinnPay &mdash; Freelancer Payment Platform</p>
    </div>
</footer>

</body>
</html>
