<?php

namespace Src\Domain\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\DriverDomain\Contracts\DriverLocationContract;
use Src\Domain\DriverDomain\Services\DriverLocationService;
use Src\Domain\OrderDomain\Contracts\OrderAssignmentContract;
use Src\Domain\OrderDomain\Services\OrderAssignmentService;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * This method is used to bind interfaces to their concrete implementations.
     * Laravel's service container will automatically resolve these bindings
     * when the interfaces are type-hinted in constructors.
     */
    public function register(): void
    {
        // Bind DriverDomain contracts
        $this->app->bind(
            DriverLocationContract::class,
            DriverLocationService::class
        );

        // Bind OrderDomain contracts
        $this->app->bind(
            OrderAssignmentContract::class,
            OrderAssignmentService::class
        );

        // Alternative: Use singleton if you want to share the same instance
        // $this->app->singleton(DriverLocationContract::class, DriverLocationService::class);
        // $this->app->singleton(OrderAssignmentContract::class, OrderAssignmentService::class);
    }

    /**
     * Bootstrap services.
     *
     * This method is called after all services are registered.
     * Use it for event listeners, route model bindings, etc.
     */
    public function boot(): void
    {
        //
    }
}
