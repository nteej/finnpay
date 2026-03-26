@extends('layouts.guest')
@section('title', 'Account Pending Verification')

@section('content')
<div class="text-center">
    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>

    <h2 class="text-xl font-bold text-slate-800 mb-2">Account Pending Verification</h2>
    <p class="text-slate-500 text-sm leading-relaxed mb-6">
        Your registration has been received. Our team is reviewing your account
        and will verify it shortly. You will be able to access FinnPay once
        an administrator approves your account.
    </p>

    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-left">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Your Account</p>
        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
        <p class="text-xs font-mono text-indigo-600 mt-1">{{ auth()->user()->freelancer_id }}</p>

        @if(auth()->user()->rejection_reason)
            <div class="mt-3 pt-3 border-t border-slate-200">
                <p class="text-xs font-medium text-red-600 mb-1">Rejection Reason:</p>
                <p class="text-xs text-red-700">{{ auth()->user()->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <div class="space-y-2">
        <p class="text-xs text-slate-400">
            Already verified? <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">Go to dashboard</a>
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 hover:underline">
                Sign out
            </button>
        </form>
    </div>
</div>
@endsection
