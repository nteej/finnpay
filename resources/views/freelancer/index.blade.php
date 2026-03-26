@extends('layouts.public')
@section('title', 'Find Freelancers')

@section('content')
{{-- Page header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Find Sri Lankan Freelancers</h1>
    <p class="text-slate-500 text-sm mt-1">Browse verified freelancers ready to work with your team.</p>
</div>

{{-- Search & filters --}}
<form method="GET" action="{{ route('freelancers.index') }}"
      class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
    <div class="flex-1 relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, title or skill…"
               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
    </div>
    <select name="category"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#003580] text-slate-700">
        <option value="">All categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
        @endforeach
    </select>
    <select name="availability"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#003580] text-slate-700">
        <option value="">Any availability</option>
        @foreach($availabilities as $key => $label)
            <option value="{{ $key }}" @selected($avail === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit"
            class="bg-[#003580] hover:bg-[#002868] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
        Search
    </button>
    @if($search || $category || $avail)
        <a href="{{ route('freelancers.index') }}"
           class="text-sm text-slate-500 hover:text-slate-700 self-center whitespace-nowrap">Clear filters</a>
    @endif
</form>

{{-- Results count --}}
<p class="text-xs text-slate-500 mb-4">
    {{ $profiles->total() }} freelancer{{ $profiles->total() !== 1 ? 's' : '' }} found
    @if($search) for "<strong class="text-slate-700">{{ $search }}</strong>"@endif
</p>

{{-- Grid --}}
@if($profiles->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 py-16 text-center">
        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-slate-500 text-sm font-medium">No freelancers found</p>
        <p class="text-slate-400 text-xs mt-1">Try adjusting your search or filters</p>
    </div>
@else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($profiles as $profile)
        @php
            $initials = strtoupper(substr($profile->user->name, 0, 1));
            $colors = ['bg-[#003580]','bg-indigo-600','bg-violet-600','bg-emerald-600','bg-rose-600'];
            $color = $colors[$profile->id % count($colors)];
            $skills = $profile->skillsArray();
        @endphp
        <a href="{{ route('freelancers.show', $profile->publicSlug()) }}"
           class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-[#003580]/40 hover:shadow-lg hover:shadow-blue-50 transition-all block group">
            {{-- Header --}}
            <div class="flex items-start gap-3 mb-4">
                <div class="w-11 h-11 rounded-full {{ $color }} flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 truncate group-hover:text-[#003580] transition-colors">
                        {{ $profile->user->name }}
                    </p>
                    <p class="text-xs text-slate-500 truncate">{{ $profile->title ?: 'Freelancer' }}</p>
                </div>
                @php
                    $availColor = match($profile->availability) {
                        'open'      => 'bg-emerald-100 text-emerald-700',
                        'part_time' => 'bg-amber-100 text-amber-700',
                        default     => 'bg-slate-100 text-slate-500',
                    };
                @endphp
                <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full {{ $availColor }}">
                    {{ $profile->availabilityLabel() }}
                </span>
            </div>

            {{-- Skills --}}
            @if($skills)
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach(array_slice($skills, 0, 4) as $skill)
                    <span class="text-xs bg-[#EEF4FF] text-[#003580] px-2.5 py-0.5 rounded-full font-medium">{{ $skill }}</span>
                @endforeach
                @if(count($skills) > 4)
                    <span class="text-xs bg-slate-100 text-slate-500 px-2.5 py-0.5 rounded-full">+{{ count($skills) - 4 }} more</span>
                @endif
            </div>
            @endif

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    @if($profile->location)
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $profile->location }}
                        </span>
                    @endif
                    @if($profile->workHistory->count())
                        <span>{{ $profile->workHistory->count() }} project{{ $profile->workHistory->count() !== 1 ? 's' : '' }}</span>
                    @endif
                </div>
                @if($profile->hourly_rate)
                    <span class="text-sm font-bold text-slate-800">
                        {{ $profile->hourly_rate_currency === 'EUR' ? '€' : '$' }}{{ $profile->hourly_rate }}<span class="text-xs font-normal text-slate-400">/hr</span>
                    </span>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($profiles->hasPages())
        <div class="mt-8">{{ $profiles->links() }}</div>
    @endif
@endif
@endsection
