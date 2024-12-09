<?php

namespace App\Providers;

use App\Repositories\ContractRepository;
use App\Repositories\Implementations\EloquentContractRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\Implementations\EloquentPaymentRepository;
use App\UseCases\ContractUseCases;
use App\UseCases\PaymentUseCases;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        ContractRepository::class => EloquentContractRepository::class,
        PaymentRepository::class => EloquentPaymentRepository::class,

        ContractUseCases::class => ContractUseCases::class,
        PaymentUseCases::class => PaymentUseCases::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
