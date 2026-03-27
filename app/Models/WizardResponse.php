<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WizardResponse extends Model
{
    protected $fillable = ['onboarding_wizard_id', 'wizard_question_id', 'answer'];

    public function wizard(): BelongsTo
    {
        return $this->belongsTo(OnboardingWizard::class, 'onboarding_wizard_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(WizardQuestion::class);
    }
}
