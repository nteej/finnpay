@extends('layouts.app')
@section('title', 'Releases')

@section('content')
{{-- Info Banner --}}
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
    <div class="flex-1">
        <h3 class="font-semibold text-slate-800 text-sm mb-1">Payment Release Schedule</h3>
        <p class="text-xs text-slate-500 leading-relaxed">
            Payments are released <strong>twice per month</strong> (1st &amp; 16th). Cleared transactions are converted to
            <strong>LKR</strong> and transferred to your registered bank account.
        </p>
    </div>
    <div class="flex flex-col sm:items-end gap-1.5 flex-shrink-0">
        <div class="text-xs text-slate-500">Next scheduled release</div>
        <div class="text-lg font-bold text-indigo-700">{{ $nextRelease->format('d F Y') }}</div>
        <div class="text-xs text-slate-400">{{ $pendingCount }} payment{{ $pendingCount !== 1 ? 's' : '' }} pending &middot;
            LKR {{ number_format($pendingBalance['lkr'], 2) }}
        </div>
    </div>
</div>

{{-- Manual Release --}}
@if($pendingCount > 0)
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-emerald-800">{{ $pendingCount }} payment{{ $pendingCount !== 1 ? 's' : '' }} ready to release</p>
            <p class="text-xs text-emerald-700 mt-0.5">
                Approx. LKR {{ number_format($pendingBalance['lkr'], 2) }} to your bank
                @if(!auth()->user()->hasBankDetails())
                    — <a href="{{ route('profile.edit') }}" class="underline font-medium">Add bank details first</a>
                @endif
            </p>
        </div>
        @if(auth()->user()->hasBankDetails())
            <form method="POST" action="{{ route('releases.process') }}" onsubmit="return confirm('Confirm release to bank account?')">
                @csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors whitespace-nowrap">
                    Release Now
                </button>
            </form>
        @endif
    </div>
@endif

{{-- Release History --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800 text-sm">Release History</h3>
    </div>

    @if($releases->isEmpty())
        <div class="text-center py-14">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-slate-500 text-sm">No releases yet</p>
            <p class="text-slate-400 text-xs mt-1">Your first release will appear here after it's processed</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Release Code</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Period</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount (LKR)</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Txns</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Processed</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($releases as $release)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-semibold text-slate-700">{{ $release->release_code }}</span>
                            </td>
                            <td class="px-5 py-3.5 hidden sm:table-cell text-slate-600 text-xs">
                                {{ $release->period_start->format('d M') }} – {{ $release->period_end->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                LKR {{ number_format($release->total_lkr, 2) }}
                            </td>
                            <td class="px-5 py-3.5 hidden md:table-cell text-slate-600">{{ $release->transaction_count }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $release->status_badge }}">
                                    {{ ucfirst($release->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell text-slate-500 text-xs">
                                {{ $release->processed_at ? $release->processed_at->format('d M Y, H:i') : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('releases.show', $release) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($releases->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $releases->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
