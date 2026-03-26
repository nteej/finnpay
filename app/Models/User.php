<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'freelancer_id', 'phone',
        'local_currency', 'is_active',
        'is_admin', 'is_verified', 'verified_at', 'verified_by', 'rejection_reason',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at'       => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_admin'          => 'boolean',
            'is_verified'       => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }

    public function paymentReferences()
    {
        return $this->hasMany(PaymentReference::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function releases()
    {
        return $this->hasMany(Release::class);
    }

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function activeUserPackage(): ?UserPackage
    {
        return $this->userPackages()->where('is_active', true)->with('package')->latest()->first();
    }

    public function activePackage(): ?ReleasePackage
    {
        return $this->activeUserPackage()?->package;
    }

    public function canChangePackage(): bool
    {
        $sub = $this->activeUserPackage();
        return ! $sub || now()->gte($sub->locked_until);
    }

    /** Returns the active package if set, otherwise falls back to global ReleaseCycleSetting */
    public function cycleSettings(): ReleasePackage|ReleaseCycleSetting
    {
        return $this->activePackage() ?? ReleaseCycleSetting::current();
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class)->where('is_active', true)->orderByDesc('is_default');
    }

    public function defaultBankAccount(): ?BankAccount
    {
        return $this->bankAccounts()->first();
    }

    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function getOrCreateProfile(): FreelancerProfile
    {
        return $this->freelancerProfile ?? $this->freelancerProfile()->create(['user_id' => $this->id]);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function pendingBalance(): array
    {
        $cleared = $this->transactions()->where('status', 'cleared')->get();

        return [
            'usd' => round($cleared->whereNotNull('final_usd')->sum('final_usd'), 2),
            'eur' => round($cleared->whereNotNull('final_eur')->sum('final_eur'), 2),
            'lkr' => round($cleared->whereNotNull('final_lkr')->sum('final_lkr'), 2),
        ];
    }

    public function hasBankDetails(): bool
    {
        return $this->bankAccounts()->exists();
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->freelancer_id) && ! $user->is_admin) {
                do {
                    $id = 'FPL-' . strtoupper(substr(md5(uniqid()), 0, 6));
                } while (static::where('freelancer_id', $id)->exists());
                $user->freelancer_id = $id;
            }
        });
    }
}
