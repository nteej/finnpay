<?php

namespace App\Policies;

use App\Models\PaymentReference;
use App\Models\User;

class PaymentReferencePolicy
{
    public function view(User $user, PaymentReference $reference): bool
    {
        return $user->id === $reference->user_id;
    }

    public function delete(User $user, PaymentReference $reference): bool
    {
        return $user->id === $reference->user_id;
    }
}
