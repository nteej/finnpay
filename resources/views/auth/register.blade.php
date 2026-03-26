@extends('layouts.guest')
@section('title', 'Create Account')

@section('content')
<h2 class="text-2xl font-bold text-slate-800 mb-1">Create your account</h2>
<p class="text-slate-500 text-sm mb-6">Join FinnPay to receive freelance payments globally</p>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full name</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent transition"
               placeholder="John Doe">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent transition"
               placeholder="you@example.com">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone <span class="text-slate-400">(optional)</span></label>
        <input type="tel" name="phone" value="{{ old('phone') }}"
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent transition"
               placeholder="+94 77 123 4567">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
        <input type="password" name="password" required
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent transition"
               placeholder="Minimum 8 characters">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm password</label>
        <input type="password" name="password_confirmation" required
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent transition"
               placeholder="Re-enter your password">
    </div>

    <button type="submit"
            class="w-full bg-[#003580] hover:bg-[#002868] text-white font-semibold py-2.5 px-4 rounded-lg
                   transition-colors text-sm mt-2">
        Create account
    </button>
</form>

<p class="text-center text-sm text-slate-500 mt-6">
    Already have an account?
    <a href="{{ route('login') }}" class="text-[#003580] font-medium hover:underline">Sign in</a>
</p>
@endsection
