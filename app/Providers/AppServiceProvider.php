<?php

namespace App\Providers;

use App\Services\PaymentAdapterInterface;
use App\Services\Adapters\StripePaymentAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentAdapterInterface::class, StripePaymentAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
