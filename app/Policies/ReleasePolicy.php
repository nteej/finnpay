<?php

namespace App\Policies;

use App\Models\Release;
use App\Models\User;

class ReleasePolicy
{
    public function view(User $user, Release $release): bool
    {
        return $user->id === $release->user_id;
    }
}
