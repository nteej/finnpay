<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $fillable = [
        'user_id', 'release_package_id',
        'started_at', 'locked_until',
        'is_active', 'changed_by',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'locked_until' => 'datetime',
        'is_active'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(ReleasePackage::class, 'release_package_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function isLocked(): bool
    {
        return now()->lt($this->locked_until);
    }
}
