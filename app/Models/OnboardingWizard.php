<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingWizard extends Model
{
    protected $fillable = [
        'user_id', 'token', 'sent_at', 'completed_at', 'admin_notes',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(WizardResponse::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isPending(): bool
    {
        return $this->sent_at !== null && $this->completed_at === null;
    }
}
