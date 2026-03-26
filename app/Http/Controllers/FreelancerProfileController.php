<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use App\Models\WorkHistoryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancerProfileController extends Controller
{
    public function edit()
    {
        $user        = Auth::user();
        $profile     = $user->freelancerProfile;
        $workHistory = $profile?->workHistory()->get() ?? collect();
        $categories  = FreelancerProfile::CATEGORIES;
        $availabilities = FreelancerProfile::AVAILABILITIES;

        return view('freelancer.profile.edit', compact('user', 'profile', 'workHistory', 'categories', 'availabilities'));
    }

    public function update(Request $request)
    {
        $user    = Auth::user();
        $profile = $user->freelancerProfile;

        $data = $request->validate([
            'title'                => 'nullable|string|max:100',
            'bio'                  => 'nullable|string|max:1500',
            'skills'               => 'nullable|string|max:500',
            'hourly_rate'          => 'nullable|integer|min:1|max:9999',
            'hourly_rate_currency' => 'nullable|in:USD,EUR',
            'availability'         => 'nullable|in:open,part_time,unavailable',
            'location'             => 'nullable|string|max:100',
            'website'              => 'nullable|url|max:255',
            'category'             => 'nullable|string|max:100',
            'username'             => [
                'nullable', 'string', 'max:50', 'alpha_dash',
                'unique:freelancer_profiles,username,' . ($profile?->id ?? 'NULL'),
            ],
            'is_public' => 'boolean',
        ]);

        $data['is_public']  = $request->boolean('is_public');
        $data['user_id']    = $user->id;

        $user->freelancerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('success', 'Public profile updated successfully.');
    }

    public function storeWork(Request $request)
    {
        $data = $request->validate([
            'project_title' => 'required|string|max:200',
            'description'   => 'nullable|string|max:1000',
            'client_name'   => 'nullable|string|max:100',
            'category'      => 'nullable|string|max:100',
            'completed_at'  => 'nullable|date|before_or_equal:today',
            'is_featured'   => 'boolean',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');

        $profile = Auth::user()->getOrCreateProfile();
        $profile->workHistory()->create($data);

        return back()->with('success', 'Work entry added.');
    }

    public function destroyWork(WorkHistoryEntry $entry)
    {
        abort_unless($entry->profile->user_id === Auth::id(), 403);
        $entry->delete();

        return back()->with('success', 'Work entry removed.');
    }
}
