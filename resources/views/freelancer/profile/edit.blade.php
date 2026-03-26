@extends('layouts.app')
@section('title', 'Public Profile')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Visibility banner --}}
    @if($profile && $profile->is_public)
        <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
            <div class="flex items-center gap-2 text-sm text-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Your profile is <strong>public</strong> —
                <a href="{{ route('freelancers.show', $profile->publicSlug()) }}"
                   target="_blank" class="underline font-medium">view it live</a>
            </div>
        </div>
    @elseif($profile && !$profile->is_public)
        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
            Profile is hidden — enable "Make profile public" below to appear in the directory.
        </div>
    @endif

    {{-- Profile details --}}
    <form method="POST" action="{{ route('freelancer.profile.update') }}">
        @csrf @method('PATCH')

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-5">Profile Details</h2>
            <div class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Professional Title
                        </label>
                        <input type="text" name="title" value="{{ old('title', $profile?->title) }}"
                               placeholder="e.g. Full-Stack Developer"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Bio</label>
                        <textarea name="bio" rows="4" placeholder="Tell employers a bit about yourself…"
                                  class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent resize-none">{{ old('bio', $profile?->bio) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Skills
                            <span class="font-normal normal-case text-slate-400 ml-1">comma-separated, e.g. Laravel, Vue.js, Figma</span>
                        </label>
                        <input type="text" name="skills" value="{{ old('skills', $profile?->skills) }}"
                               placeholder="Laravel, React, Tailwind CSS, MySQL…"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category"
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $profile?->category) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Availability</label>
                        <select name="availability"
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                            @foreach($availabilities as $key => $label)
                                <option value="{{ $key }}" @selected(old('availability', $profile?->availability ?? 'open') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hourly Rate</label>
                            <input type="number" name="hourly_rate" value="{{ old('hourly_rate', $profile?->hourly_rate) }}"
                                   min="1" max="9999" placeholder="75"
                                   class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Currency</label>
                            <select name="hourly_rate_currency"
                                    class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                                <option value="USD" @selected(old('hourly_rate_currency', $profile?->hourly_rate_currency ?? 'USD') === 'USD')>USD ($)</option>
                                <option value="EUR" @selected(old('hourly_rate_currency', $profile?->hourly_rate_currency) === 'EUR')>EUR (€)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" value="{{ old('location', $profile?->location) }}"
                               placeholder="Colombo, Sri Lanka"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Website</label>
                        <input type="url" name="website" value="{{ old('website', $profile?->website) }}"
                               placeholder="https://yourwebsite.com"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Public Username
                            <span class="font-normal normal-case text-slate-400 ml-1">optional — used in your public URL</span>
                        </label>
                        <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-[#003580] focus-within:border-transparent">
                            <span class="px-3.5 py-2.5 bg-slate-50 border-r border-slate-300 text-xs text-slate-500 whitespace-nowrap">/freelancers/</span>
                            <input type="text" name="username" value="{{ old('username', $profile?->username) }}"
                                   placeholder="{{ auth()->user()->freelancer_id }}"
                                   class="flex-1 px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none bg-white">
                        </div>
                    </div>
                </div>

                {{-- Visibility toggle --}}
                <div class="flex items-center justify-between p-4 bg-[#F0F6FF] border border-blue-100 rounded-xl mt-2">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Make profile public</p>
                        <p class="text-xs text-slate-500 mt-0.5">Visible in the freelancer directory for employers to find</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" value="1" class="sr-only peer"
                               @if(old('is_public', $profile?->is_public)) checked @endif>
                        <div class="w-10 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#003580] rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003580]"></div>
                    </label>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-[#003580] hover:bg-[#002868] text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Save profile
                </button>
            </div>
        </div>
    </form>

    {{-- Work History --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Work History</h2>
            <p class="text-xs text-slate-500 mt-0.5">Showcase completed projects to potential employers.</p>
        </div>

        {{-- Existing entries --}}
        @if($workHistory->isNotEmpty())
        <div class="divide-y divide-slate-100">
            @foreach($workHistory as $entry)
            <div class="flex items-start justify-between gap-3 px-6 py-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-medium text-slate-800">{{ $entry->project_title }}</p>
                        @if($entry->is_featured)
                            <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-medium">Featured</span>
                        @endif
                        @if($entry->category)
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $entry->category }}</span>
                        @endif
                    </div>
                    @if($entry->client_name)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $entry->client_name }}</p>
                    @endif
                    @if($entry->completed_at)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $entry->completed_at->format('M Y') }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('freelancer.profile.work.destroy', $entry) }}"
                      onsubmit="return confirm('Remove this entry?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium flex-shrink-0">Remove</button>
                </form>
            </div>
            @endforeach
        </div>
        @else
            <div class="px-6 py-8 text-center text-sm text-slate-400">No work history yet — add your first entry below.</div>
        @endif

        {{-- Add new entry --}}
        <div class="px-6 py-5 border-t border-slate-100 bg-slate-50 rounded-b-xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Add Work Entry</p>
            <form method="POST" action="{{ route('freelancer.profile.work.store') }}" class="space-y-3">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <input type="text" name="project_title" placeholder="Project title *" required
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                    </div>
                    <div>
                        <input type="text" name="client_name" placeholder="Client name (optional)"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                    </div>
                    <div>
                        <select name="category"
                                class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] bg-white">
                            <option value="">Category (optional)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="completed_at" max="{{ now()->toDateString() }}"
                               class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured"
                               class="w-4 h-4 rounded border-slate-300 text-[#003580] focus:ring-[#003580]">
                        <label for="is_featured" class="text-sm text-slate-600">Mark as featured</label>
                    </div>
                    <div class="sm:col-span-2">
                        <textarea name="description" rows="2" placeholder="Brief project description (optional)"
                                  class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
                        Add entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
