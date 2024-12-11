<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Plan as EloquentPlan;
use App\Repositories\Implementations\EloquentPlanRepository;
use App\Domain\Entities\Plan;
use Tests\TestCase;

class EloquentPlanRepositoryTest extends TestCase
{
    /** 
     * Note: `use RefreshDatabase;` causes errors in parallel tests. 
     * DatabaseMigrations is slower but works.
     */
    use DatabaseMigrations;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->repository = new EloquentPlanRepository();
    }

    public function test_it_get_plans_should_return_all_plans()
    {
        $seedPlans = [
            [
                'description' => 'Individual',
                'number_of_clients' => 1,
                'price' => 9.90,
                'gigabytes_storage' => 1,
            ], [
                'description' => 'Até 10 vistorias / clientes ativos',
                'number_of_clients' => 10,
                'price' => 87.00,
                'gigabytes_storage' => 10,
            ], [
                'description' => 'Até 25 vistorias / clientes ativos',
                'number_of_clients' => 25,
                'price' => 197.00,
                'gigabytes_storage' => 25,
            ], [
                'description' => 'Até 50 vistorias / clientes ativos',
                'number_of_clients' => 50,
                'price' => 347.00,
                'gigabytes_storage' => 50,
            ], [
                'description' => 'Até 100 vistorias / clientes ativos',
                'number_of_clients' => 100,
                'price' => 497.00,
                'gigabytes_storage' => 100,
            ], [
                'description' => 'Até 250 vistorias / clientes ativos',
                'number_of_clients' => 250,
                'price' => 797.00,
                'gigabytes_storage' => 25,
            ]
        ];

        $plans = $this->repository->fetchAll();

        foreach ($plans as $key => $plan) {
            $a = $seedPlans[$key];
            $b = $plan->toArray();

            $this->assertInstanceOf(Plan::class, $plan);
            $this->assertEquals($a['description'], $b['description']);
            $this->assertEquals($a['number_of_clients'], $b['number_of_clients']);
            $this->assertEquals($a['price'], $b['price']);
            $this->assertEquals($a['gigabytes_storage'], $b['gigabytes_storage']);
        }
    }

    public function test_it_get_plan_by_id_should_return_correct_plan()
    {
        $expectedPlan = [
            'description' => 'Até 9000 vistorias / clientes ativos',
            'number_of_clients' => 5689,
            'price' => 65.89,
            'gigabytes_storage' => 1245,
        ];
        
        $newPlan = new EloquentPlan();
        $newPlan->description = $expectedPlan['description'];
        $newPlan->number_of_clients = $expectedPlan['number_of_clients'];
        $newPlan->price = $expectedPlan['price'];
        $newPlan->gigabytes_storage = $expectedPlan['gigabytes_storage'];
        $newPlan->save();

        $expectedPlanId = $newPlan?->id;

        $plan = $this->repository->getById($expectedPlanId);

        $this->assertNotNull($plan);
        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertEquals($expectedPlan['description'], $plan->description());
        $this->assertEquals($expectedPlan['number_of_clients'], $plan->numberOfClients());
        $this->assertEquals($expectedPlan['price'], $plan->price()->value());
        $this->assertEquals($expectedPlan['gigabytes_storage'], $plan->gigabytesStorage());
    }
}
