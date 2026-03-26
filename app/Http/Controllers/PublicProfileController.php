<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;

class PublicProfileController extends Controller
{
    public function index()
    {
        $search   = request('search');
        $category = request('category');
        $avail    = request('availability');

        $profiles = FreelancerProfile::public()
            ->with(['user', 'workHistory'])
            ->search($search)
            ->byCategory($category)
            ->byAvailability($avail)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories    = FreelancerProfile::CATEGORIES;
        $availabilities = FreelancerProfile::AVAILABILITIES;

        return view('freelancer.index', compact('profiles', 'categories', 'availabilities', 'search', 'category', 'avail'));
    }

    public function show(string $slug)
    {
        // Try username first, then fall back to freelancer_id
        $profile = FreelancerProfile::public()
            ->with(['user', 'workHistory'])
            ->where('username', $slug)
            ->first();

        if (! $profile) {
            $profile = FreelancerProfile::public()
                ->with(['user', 'workHistory'])
                ->whereHas('user', fn ($q) => $q->where('freelancer_id', $slug))
                ->firstOrFail();
        }

        return view('freelancer.show', compact('profile'));
    }
}
