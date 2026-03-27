<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WizardQuestion extends Model
{
    const TYPES = [
        'text'     => 'Short Text',
        'textarea' => 'Long Text',
        'number'   => 'Number',
        'date'     => 'Date',
        'select'   => 'Dropdown',
        'radio'    => 'Single Choice',
        'checkbox' => 'Multiple Choice',
        'boolean'  => 'Yes / No',
    ];

    protected $fillable = [
        'section', 'question_text', 'helper_text',
        'type', 'options', 'is_required', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(WizardResponse::class);
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']);
    }
}
