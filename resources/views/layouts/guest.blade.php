<!DOCTYPE html>
<html lang="en" class="h-full bg-[#003580]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — FinnPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-11 h-11 rounded-2xl bg-white flex items-center justify-center shadow-lg">
                <span class="text-[#003580] font-black text-sm tracking-tight">FP</span>
            </div>
            <span class="text-white font-bold text-2xl tracking-tight">FinnPay</span>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            @yield('content')
        </div>

        <p class="text-center text-blue-200 text-xs mt-6">
            &copy; {{ date('Y') }} FinnPay &mdash; Freelancer Payment Platform
        </p>
    </div>
</body>
</html>
