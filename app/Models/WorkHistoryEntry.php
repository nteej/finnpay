<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHistoryEntry extends Model
{
    protected $fillable = [
        'freelancer_profile_id', 'project_title', 'description',
        'client_name', 'category', 'completed_at', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'completed_at' => 'date',
        'is_featured'  => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(FreelancerProfile::class, 'freelancer_profile_id');
    }
}
