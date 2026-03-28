@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- Onboarding status banners --}}
@php $status = auth()->user()->onboardingStatus(); @endphp
@if($status === 'no_wizard')
    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 mb-5">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-800">Onboarding not started</p>
            <p class="text-xs text-amber-700 mt-0.5">Your account manager will send you an onboarding questionnaire link shortly. Payment features will be enabled once complete.</p>
        </div>
    </div>
@elseif($status === 'wizard_pending')
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-4 mb-5">
        <svg class="w-5 h-5 text-[#003580] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-[#003580]">Complete your onboarding</p>
            <p class="text-xs text-blue-700 mt-0.5">
                You have a pending onboarding questionnaire.
                <a href="{{ route('onboarding.show', auth()->user()->onboardingWizard->token) }}"
                   class="font-semibold underline">Complete it now →</a>
            </p>
        </div>
    </div>
@elseif($status === 'pending_package')
    <div class="flex items-start gap-3 bg-violet-50 border border-violet-200 rounded-xl px-4 py-4 mb-5">
        <svg class="w-5 h-5 text-violet-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-violet-800">Questionnaire received — awaiting package assignment</p>
            <p class="text-xs text-violet-700 mt-0.5">Your answers have been reviewed. Our team will assign your release package shortly and activate your account.</p>
        </div>
    </div>
@endif

{{-- Welcome banner --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-slate-800">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }}</h2>
    <p class="text-slate-500 text-sm mt-0.5">Your freelancer ID: <span class="font-mono font-semibold text-[#003580]">{{ auth()->user()->freelancer_id }}</span></p>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    {{-- Pending Balance --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pending Balance</span>
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">LKR {{ number_format($pendingBalance['lkr'], 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">
            @if($pendingBalance['usd'] > 0) ${{ number_format($pendingBalance['usd'], 2) }} @endif
            @if($pendingBalance['eur'] > 0) €{{ number_format($pendingBalance['eur'], 2) }} @endif
        </p>
    </div>

    {{-- Total Received --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Received</span>
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">€{{ number_format($totalReceived, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">All time earnings</p>
    </div>

    {{-- Total Released --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Released</span>
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">LKR {{ number_format($totalReleased, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">Released to bank</p>
    </div>

    {{-- Next Release --}}
    <div class="bg-[#003580] rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-blue-200 uppercase tracking-wider">Next Release</span>
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold">{{ $nextRelease->format('d M') }}</p>
        <p class="text-xs text-blue-200 mt-1">
            {{ $pendingCount }} payment{{ $pendingCount !== 1 ? 's' : '' }} pending
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Transactions --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800 text-sm">Recent Transactions</h3>
            <a href="{{ route('transactions.index') }}" class="text-xs text-[#003580] hover:underline font-medium">View all</a>
        </div>

        @if($recentTransactions->isEmpty())
            <div class="px-5 py-10 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-slate-500 text-sm">No transactions yet</p>
                <a href="{{ route('references.create') }}" class="text-[#003580] text-xs hover:underline mt-1 inline-block">Create a payment reference</a>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentTransactions as $tx)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-slate-600">{{ strtoupper(substr($tx->payer_name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $tx->payer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $tx->received_at->format('d M Y') }}
                                @if($tx->paymentReference)
                                    &middot; <span class="font-mono text-[#003580]">{{ $tx->paymentReference->reference_number }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $tx->display_amount }}</p>
                            <span class="inline-flex text-xs px-2 py-0.5 rounded-full font-medium {{ $tx->status_badge }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Actions + Info --}}
    <div class="space-y-4">
        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('references.create') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 bg-[#DDEEFF] rounded-lg flex items-center justify-center group-hover:bg-[#BBDDFF] transition-colors">
                        <svg class="w-4 h-4 text-[#003580]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">New Payment Reference</p>
                        <p class="text-xs text-slate-400">Generate a reference for clients</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('releases.claim') }}">
                    @csrf
                    <button type="submit" {{ $pendingCount === 0 || !auth()->user()->hasBankDetails() ? 'disabled' : '' }}
                            class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors group
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-slate-700">Raise Payment Claim</p>
                            <p class="text-xs text-slate-400">
                                @if(!auth()->user()->hasBankDetails())
                                    Add bank details first
                                @elseif($pendingCount === 0)
                                    No pending balance
                                @else
                                    Claim {{ $pendingCount }} payment{{ $pendingCount !== 1 ? 's' : '' }} for approval
                                @endif
                            </p>
                        </div>
                    </button>
                </form>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">Edit Profile</p>
                        <p class="text-xs text-slate-400">Bank account & personal info</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Release Schedule Info --}}
        <div class="bg-[#EEF4FF] rounded-xl border border-blue-200 p-5">
            <h3 class="font-semibold text-[#003580] text-sm mb-2">Release Schedule</h3>
            <p class="text-xs text-[#002868] leading-relaxed">
                Payments are automatically released <strong>twice per month</strong> — on the <strong>1st</strong> and <strong>16th</strong> of each month.
            </p>
            <div class="mt-3 space-y-1.5">
                <div class="flex justify-between text-xs">
                    <span class="text-[#003580]">Next release:</span>
                    <span class="font-semibold text-[#003580]">{{ $nextRelease->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-[#003580]">Currency:</span>
                    <span class="font-semibold text-[#003580]">LKR (Sri Lankan Rupee)</span>
                </div>
                @if($lastRelease)
                    <div class="flex justify-between text-xs">
                        <span class="text-[#003580]">Last release:</span>
                        <span class="font-semibold text-[#003580]">{{ $lastRelease->processed_at->format('d M Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
