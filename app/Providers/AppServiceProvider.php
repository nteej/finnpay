<?php

namespace App\Providers;

use App\Models\PaymentReference;
use App\Models\Release;
use App\Policies\PaymentReferencePolicy;
use App\Policies\ReleasePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        PaymentReference::class => PaymentReferencePolicy::class,
        Release::class          => ReleasePolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
