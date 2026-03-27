<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F0F6FF]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Complete — FinnPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col items-center justify-center p-5 antialiased">
    <div class="w-full max-w-md text-center">
        <div class="w-16 h-16 rounded-2xl bg-[#003580] flex items-center justify-center mx-auto mb-6">
            <span class="text-white font-black text-sm tracking-tight">FP</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-8">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-slate-800 mb-2">You're all done!</h1>
            <p class="text-slate-500 text-sm leading-relaxed">
                Thank you, <strong>{{ $wizard->user->name }}</strong>. Your onboarding questionnaire has been submitted successfully.
            </p>

            <div class="mt-6 bg-[#EEF4FF] border border-blue-100 rounded-xl p-4 text-left">
                <p class="text-xs font-semibold text-[#003580] uppercase tracking-wider mb-2">What happens next?</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#003580] flex-shrink-0 mt-1.5"></span>
                        Our team will review your answers shortly.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#003580] flex-shrink-0 mt-1.5"></span>
                        Based on your profile, we'll assign the best payment release package for you.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#003580] flex-shrink-0 mt-1.5"></span>
                        Once approved, you can log in and start collecting payments immediately.
                    </li>
                </ul>
            </div>

            <p class="text-xs text-slate-400 mt-6">
                Submitted on {{ $wizard->completed_at->format('d M Y \a\t H:i') }}
            </p>
        </div>

        <p class="text-slate-400 text-xs mt-6">&copy; {{ date('Y') }} FinnPay &mdash; Freelancer Payment Platform</p>
    </div>
</body>
</html>
