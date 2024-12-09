<?php

namespace App\Providers;

use App\Repositories\ContractRepository;
use App\Repositories\Implementations\EloquentContractRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\Implementations\EloquentPaymentRepository;
use App\UseCases\ContractsUseCases;
use App\UseCases\PaymentsUseCases;
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

        ContractsUseCases::class => ContractsUseCases::class,
        PaymentsUseCases::class => PaymentsUseCases::class,
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
