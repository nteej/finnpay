@extends('layouts.app')
@section('title', 'Release Packages')

@section('content')
<div class="max-w-3xl">

    {{-- Current package status --}}
    @if($activeSub)
        @php $pkg = $activeSub->package; @endphp
        <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Current Package</p>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-base">{{ $pkg->name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $pkg->color === 'amber' ? 'bg-amber-100 text-amber-700' :
                           ($pkg->color === 'indigo' ? 'bg-[#DDEEFF] text-[#002868]' : 'bg-slate-100 text-slate-600') }}">
                        {{ $pkg->scheduleLabel() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Min. balance: {{ $pkg->minimum_balance_lkr > 0 ? 'LKR ' . number_format($pkg->minimum_balance_lkr) : 'None' }}
                    &middot; Manual release: {{ $pkg->allow_manual_release ? 'Allowed' : 'Not allowed' }}
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                @if($activeSub->isLocked())
                    <p class="text-xs text-amber-600 font-medium">Locked until</p>
                    <p class="text-sm font-bold text-slate-800">{{ $activeSub->locked_until->format('d M Y') }}</p>
                @else
                    <p class="text-xs text-emerald-600 font-medium">Free to change</p>
                @endif
            </div>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
            You haven't selected a release package yet. Choose one below — you'll be locked in for 3 months.
        </div>
    @endif

    {{-- Package cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($packages as $package)
            @php
                $isCurrent   = $activeSub && $activeSub->release_package_id === $package->id;
                $canChange   = ! $activeSub || ! $activeSub->isLocked();
                $colorBorder = $package->color === 'amber'  ? 'border-amber-300'  :
                              ($package->color === 'indigo' ? 'border-[#003580]' : 'border-slate-200');
                $colorBg     = $package->color === 'amber'  ? 'bg-amber-50'   :
                              ($package->color === 'indigo' ? 'bg-[#EEF4FF]'  : 'bg-slate-50');
                $colorBadge  = $package->color === 'amber'  ? 'bg-amber-100 text-amber-700'   :
                              ($package->color === 'indigo' ? 'bg-[#DDEEFF] text-[#002868]' : 'bg-slate-100 text-slate-600');
                $colorBtn    = $package->color === 'amber'  ? 'bg-amber-500 hover:bg-amber-600'   :
                              ($package->color === 'indigo' ? 'bg-[#003580] hover:bg-[#002868]' : 'bg-slate-700 hover:bg-slate-800');
            @endphp
            <div class="bg-white rounded-xl border {{ $isCurrent ? $colorBorder . ' ring-2 ring-offset-1 ' . str_replace('border-', 'ring-', $colorBorder) : 'border-slate-200' }} p-5 flex flex-col">
                {{-- Header --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $colorBadge }}">{{ $package->name }}</span>
                        @if($isCurrent)
                            <span class="text-xs font-medium text-emerald-600">✓ Active</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $package->description }}</p>
                </div>

                {{-- Features --}}
                <div class="{{ $colorBg }} rounded-lg p-3 space-y-2 mb-5 flex-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Releases</span>
                        <span class="font-semibold text-slate-800">{{ $package->scheduleLabel() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Min. Balance</span>
                        <span class="font-semibold text-slate-800">
                            {{ $package->minimum_balance_lkr > 0 ? 'LKR ' . number_format($package->minimum_balance_lkr) : 'None' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Manual Release</span>
                        <span class="font-semibold {{ $package->allow_manual_release ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $package->allow_manual_release ? 'Allowed' : 'Not allowed' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Lock Period</span>
                        <span class="font-semibold text-slate-800">3 months</span>
                    </div>
                </div>

                {{-- Action --}}
                @if($isCurrent)
                    <div class="w-full text-center py-2 text-xs font-medium text-slate-400">Current package</div>
                @elseif(! $canChange)
                    <div class="w-full text-center py-2 text-xs font-medium text-amber-600">
                        Locked until {{ $activeSub->locked_until->format('d M Y') }}
                    </div>
                @else
                    <form method="POST" action="{{ route('packages.select', $package) }}"
                          onsubmit="return confirm('Switch to {{ $package->name }} package? You will be locked in for 3 months.')">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $colorBtn }} text-white text-xs font-semibold py-2.5 rounded-lg transition-colors">
                            Select {{ $package->name }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <p class="text-xs text-slate-400 text-center mt-4">
        Once selected, your package is locked for 3 months. Contact admin to change earlier.
    </p>
</div>
@endsection
