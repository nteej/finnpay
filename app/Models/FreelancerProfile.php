<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FreelancerProfile extends Model
{
    const CATEGORIES = [
        'Web Development', 'Mobile Development', 'UI/UX Design',
        'Graphic Design', 'Content Writing', 'SEO & Marketing',
        'Video & Animation', 'DevOps & Cloud', 'Data & Analytics', 'Other',
    ];

    const AVAILABILITIES = [
        'open'        => 'Available',
        'part_time'   => 'Part-time',
        'unavailable' => 'Not available',
    ];

    protected $fillable = [
        'user_id', 'title', 'bio', 'skills',
        'hourly_rate', 'hourly_rate_currency',
        'availability', 'location', 'website',
        'category', 'username', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workHistory(): HasMany
    {
        return $this->hasMany(WorkHistoryEntry::class)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('completed_at');
    }

    public function skillsArray(): array
    {
        if (empty($this->skills)) return [];
        return array_values(array_filter(array_map('trim', explode(',', $this->skills))));
    }

    public function availabilityLabel(): string
    {
        return self::AVAILABILITIES[$this->availability] ?? 'Unknown';
    }

    public function publicSlug(): string
    {
        return $this->username ?? $this->user->freelancer_id;
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) return $query;
        return $query->where(function (Builder $q) use ($term) {
            $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"))
              ->orWhere('title', 'like', "%{$term}%")
              ->orWhere('skills', 'like', "%{$term}%");
        });
    }

    public function scopeByCategory(Builder $query, ?string $cat): Builder
    {
        return $cat ? $query->where('category', $cat) : $query;
    }

    public function scopeByAvailability(Builder $query, ?string $avail): Builder
    {
        return $avail ? $query->where('availability', $avail) : $query;
    }
}
