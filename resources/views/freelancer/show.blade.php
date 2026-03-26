@extends('layouts.public')
@section('title', $profile->user->name)

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Breadcrumb --}}
    <a href="{{ route('freelancers.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to directory
    </a>

    {{-- Profile hero --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-start gap-5">
            {{-- Avatar --}}
            @php
                $colors = ['bg-[#003580]','bg-indigo-600','bg-violet-600','bg-emerald-600','bg-rose-600'];
                $color = $colors[$profile->id % count($colors)];
                $skills = $profile->skillsArray();
                $availColor = match($profile->availability) {
                    'open'      => 'bg-emerald-100 text-emerald-700',
                    'part_time' => 'bg-amber-100 text-amber-700',
                    default     => 'bg-slate-100 text-slate-500',
                };
            @endphp
            <div class="w-16 h-16 rounded-2xl {{ $color }} flex items-center justify-center text-white font-bold text-2xl flex-shrink-0">
                {{ strtoupper(substr($profile->user->name, 0, 1)) }}
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-xl font-bold text-slate-800">{{ $profile->user->name }}</h1>
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $availColor }}">
                        {{ $profile->availabilityLabel() }}
                    </span>
                </div>
                @if($profile->title)
                    <p class="text-slate-600 font-medium">{{ $profile->title }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-slate-500">
                    @if($profile->location)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $profile->location }}
                        </span>
                    @endif
                    @if($profile->website)
                        <a href="{{ $profile->website }}" target="_blank" rel="noopener"
                           class="flex items-center gap-1 text-[#003580] hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Website
                        </a>
                    @endif
                    @if($profile->hourly_rate)
                        <span class="font-semibold text-slate-700">
                            {{ $profile->hourly_rate_currency === 'EUR' ? '€' : '$' }}{{ $profile->hourly_rate }}/hr
                        </span>
                    @endif
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex-shrink-0">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 bg-[#003580] hover:bg-[#002868] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    Hire {{ explode(' ', $profile->user->name)[0] }}
                </a>
            </div>
        </div>

        {{-- Bio --}}
        @if($profile->bio)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-sm text-slate-600 leading-relaxed">{{ $profile->bio }}</p>
            </div>
        @endif

        {{-- Skills --}}
        @if($skills)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Skills</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        <span class="text-sm bg-[#EEF4FF] text-[#003580] px-3 py-1 rounded-full font-medium">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Work History --}}
    @if($profile->workHistory->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Work History</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ $profile->workHistory->count() }} completed project{{ $profile->workHistory->count() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($profile->workHistory as $entry)
            <div class="px-6 py-4">
                <div class="flex items-start justify-between gap-3 mb-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-medium text-slate-800 text-sm">{{ $entry->project_title }}</h3>
                        @if($entry->is_featured)
                            <span class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-medium">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Featured
                            </span>
                        @endif
                    </div>
                    <div class="flex-shrink-0 flex items-center gap-2">
                        @if($entry->category)
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $entry->category }}</span>
                        @endif
                        @if($entry->completed_at)
                            <span class="text-xs text-slate-400">{{ $entry->completed_at->format('M Y') }}</span>
                        @endif
                    </div>
                </div>
                @if($entry->client_name)
                    <p class="text-xs text-slate-500 mb-1">Client: <span class="font-medium text-slate-700">{{ $entry->client_name }}</span></p>
                @endif
                @if($entry->description)
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $entry->description }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
