<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Contract as EloquentContract;
use App\Models\User as EloquentUser;
use App\Repositories\Implementations\EloquentUserRepository;
use App\Domain\Entities\User;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
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
        $this->repository = new EloquentUserRepository();
    }

    public function test_it_get_user_by_id_without_active_contract()
    {
        $anotherUserModel = new EloquentUser();
        $anotherUserModel->name = 'John Doe';
        $anotherUserModel->email = '8V4t8@example.com';
        $anotherUserModel->save();
        $anotherUserId = $anotherUserModel?->id ?? null;

        $userModel = new EloquentUser();
        $userModel->name = 'John Doe';
        $userModel->email = '8V4t8@example.com';
        $userModel->save();

        $userId = $userModel?->id ?? null;

        EloquentContract::create([
            'user_id' => $userId,
            'plan_id' => 1,
            'active' => false
        ]);

        EloquentContract::create([
            'user_id' => $anotherUserId,
            'plan_id' => 1,
            'active' => true
        ]);

        $this->assertNotNull($userId);

        $user = $this->repository->getById($userId);

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->id());
        $this->assertNull($user->activeContractId());
        $this->assertFalse($user->hasActiveContract());
    }

    public function test_it_get_user_by_id_with_active_contract()
    {
        $userModel = new EloquentUser();
        $userModel->name = 'John Doe';
        $userModel->email = '8V4t8@example.com';
        $userModel->save();

        $userId = $userModel?->id ?? null;

        EloquentContract::create([
            'user_id' => $userId,
            'plan_id' => 1,
            'active' => false
        ]);

        EloquentContract::create([
            'user_id' => $userId,
            'plan_id' => 1,
            'active' => false
        ]);

        EloquentContract::create([
            'user_id' => $userId,
            'plan_id' => 1,
            'active' => false
        ]);

        $contractId = EloquentContract::create([
            'user_id' => $userId,
            'plan_id' => 1,
            'active' => true
        ])?->id ?? null;

        $this->assertNotNull($userId);
        $this->assertNotNull($contractId);

        $user = $this->repository->getById($userId);

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->id());
        $this->assertEquals($contractId, $user->activeContractId());
        $this->assertTrue($user->hasActiveContract());
    }
}
