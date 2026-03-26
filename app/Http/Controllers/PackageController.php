<?php

namespace App\Http\Controllers;

use App\Models\ReleasePackage;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $packages      = ReleasePackage::where('is_active', true)->orderBy('sort_order')->get();
        $user          = Auth::user();
        $activeSub     = $user->activeUserPackage();

        return view('packages.index', compact('packages', 'activeSub'));
    }

    public function select(ReleasePackage $package)
    {
        $user = Auth::user();

        if (! $user->canChangePackage()) {
            $locked = $user->activeUserPackage()->locked_until;
            return back()->with('error', 'You cannot change your package until ' . $locked->format('d M Y') . '.');
        }

        $user->userPackages()->where('is_active', true)->update(['is_active' => false]);

        $user->userPackages()->create([
            'release_package_id' => $package->id,
            'started_at'         => now(),
            'locked_until'       => now()->addMonths(3),
            'is_active'          => true,
        ]);

        return back()->with('success', 'Switched to ' . $package->name . ' package. Locked until ' . now()->addMonths(3)->format('d M Y') . '.');
    }
}
