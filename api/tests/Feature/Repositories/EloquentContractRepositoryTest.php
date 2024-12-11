<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Contract as EloquentContract;
use App\Repositories\Implementations\EloquentContractRepository;
use App\Domain\Entities\Contract;
use Tests\TestCase;

class EloquentContractRepositoryTest extends TestCase
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
        $this->repository = new EloquentContractRepository();
    }

    public function test_it_creates_a_contract()
    {
        $contract = [
            'user_id' => 1,
            'plan_id' => 1,
            'active' => true
        ];
        $contract = Contract::fromArray($contract);

        $contractId = $this->repository->create($contract);

        $this->assertDatabaseHas('contracts', [
            'id' => $contractId,
            'user_id' => 1,
            'plan_id' => 1,
            'active' => true
        ]);
    }

    public function test_it_updates_a_contract()
    {
        $contract = EloquentContract::create([
            'user_id' => 1,
            'plan_id' => 1,
            'active' => true
        ]);

        $updatedContract = [
            'id' => $contract->id,
            'user_id' => 1,
            'plan_id' => 1,
            'active' => false
        ];
        $updatedContract = Contract::fromArray($updatedContract);

        $result = $this->repository->update($updatedContract);

        $this->assertTrue($result);
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'active' => false
        ]);
    }

    public function test_it_throws_exception_when_payment_not_found_on_update()
    {
        $updatedContract = [
            'id' => 9999,
            'user_id' => 1,
            'plan_id' => 1,
            'active' => false
        ];
        $updatedContract = Contract::fromArray($updatedContract);

        $result = $this->repository->update($updatedContract);

        $this->assertFalse($result);
    }

    public function test_it_fetches_a_contract_by_id()
    {
        $eloquentContract = EloquentContract::create([
            'user_id' => 1,
            'plan_id' => 1,
            'active' => true
        ]);

        $contract = $this->repository->getById($eloquentContract->id);

        $this->assertNotNull($contract);
        $this->assertEquals($eloquentContract->id, $contract->id());
    }

    public function test_it_returns_null_if_contract_not_found_by_id()
    {
        $contract = $this->repository->getById(9999);

        $this->assertNull($contract);
    }

    public function test_it_fetches_all_contracts()
    {
        EloquentContract::create(['user_id' => 1, 'plan_id' => 1, 'active' => true]);
        EloquentContract::create(['user_id' => 1, 'plan_id' => 1, 'active' => false]);

        $contracts = $this->repository->fetchAll(1);

        $this->assertCount(2, $contracts);
    }

    public function test_it_fetches_active_contracts_by_user_id()
    {
        EloquentContract::create(['user_id' => 1, 'plan_id' => 1, 'active' => true]);
        EloquentContract::create(['user_id' => 1, 'plan_id' => 1, 'active' => false]);
        EloquentContract::create(['user_id' => 1, 'plan_id' => 1, 'active' => true]);

        $contracts = $this->repository->fetchActiveContracts(1);

        $this->assertCount(2, $contracts);
        $this->assertTrue($contracts[0]->active());
    }
}
