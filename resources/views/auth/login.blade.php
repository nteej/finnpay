@extends('layouts.guest')
@section('title', 'Sign In')

@section('content')
<h2 class="text-2xl font-bold text-slate-800 mb-1">Welcome back</h2>
<p class="text-slate-500 text-sm mb-6">Sign in to your FinnPay account</p>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="you@example.com">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
        <input type="password" name="password" required
               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="••••••••">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600">
            Remember me
        </label>
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg
                   transition-colors text-sm mt-2">
        Sign in
    </button>
</form>

<p class="text-center text-sm text-slate-500 mt-6">
    Don't have an account?
    <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">Register as a freelancer</a>
</p>
@endsection
