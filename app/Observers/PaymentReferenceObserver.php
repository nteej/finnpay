<?php

namespace App\Observers;

use App\Models\PaymentReference;

class PaymentReferenceObserver
{
    /**
     * When a payment reference is created, auto-generate a hidden work history entry
     * linked to it. It will become public once the customer pays.
     */
    public function created(PaymentReference $reference): void
    {
        $profile = $reference->user->freelancerProfile ?? null;

        if (! $profile) return;

        $profile->workHistory()->create([
            'project_title'        => $reference->title,
            'description'          => $reference->notes,
            'is_public'            => false,   // hidden until paid
            'is_featured'          => false,
            'payment_reference_id' => $reference->id,
        ]);
    }

    /**
     * When the reference status changes to 'paid', make the linked work history
     * entry public and fill in the client name and completion date.
     */
    public function updated(PaymentReference $reference): void
    {
        if (! $reference->wasChanged('status') || $reference->status !== 'paid') {
            return;
        }

        $entry = $reference->workHistoryEntry;

        if (! $entry) return;

        // Pull client name from the first transaction on this reference
        $payerName = $reference->transactions()
            ->orderBy('created_at')
            ->value('payer_name');

        $entry->update([
            'is_public'    => true,
            'client_name'  => $payerName,
            'completed_at' => now()->toDateString(),
        ]);
    }
}
