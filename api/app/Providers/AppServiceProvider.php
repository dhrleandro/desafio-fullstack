<?php

namespace App\Providers;

use App\Domain\Services\ContractPaymentService;
use App\Domain\Services\SwitchContractService;
use App\Repositories\Implementations\EloquentPlanRepository;
use App\Repositories\PlanRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Repositories\ContractRepository;
use App\Repositories\Implementations\EloquentContractRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\Implementations\EloquentPaymentRepository;
use App\Repositories\UserRepository;
use App\Repositories\Implementations\EloquentUserRepository;
use App\CQS\Commands;
use App\CQS\Queries;


class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        UserRepository::class => EloquentUserRepository::class,
        PlanRepository::class => EloquentPlanRepository::class,
        ContractRepository::class => EloquentContractRepository::class,
        PaymentRepository::class => EloquentPaymentRepository::class,

        ContractPaymentService::class => ContractPaymentService::class,
        SwitchContractService::class => SwitchContractService::class,

        Commands::class => Commands::class,
        Queries::class => Queries::class,
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
