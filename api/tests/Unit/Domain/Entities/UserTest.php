<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_create_returns_instance_with_default_values()
    {
        $id = 1;
        $activeContractId = 5;

        $user = User::create($id, $activeContractId);

        $this->assertEquals($id, $user->id());
        $this->assertEquals($activeContractId, $user->activeContractId());
    }

    public function test_from_array_returns_correct_instance()
    {
        $data = [
            'id' => 15,
            'active_contract_id' => 8,
        ];

        $user = User::fromArray($data);

        $this->assertEquals($data['id'], $user->id());
        $this->assertEquals($data['active_contract_id'], $user->activeContractId());
    }

    public function test_to_array_returns_correct_array()
    {
        $data = [
            'id' => 15,
            'active_contract_id' => 8,
        ];

        $user = User::fromArray($data);

        $this->assertEquals($data, $user->toArray());
    }

    public function test_has_active_contract_returns_true()
    {
        $data = [
            'id' => 28,
            'active_contract_id' => 27,
        ];

        $user = User::fromArray($data);

        $this->assertTrue($user->hasActiveContract());
    }

    public function test_has_active_contract_returns_false()
    {
        $data = [
            'id' => 5
        ];

        $user = User::fromArray($data);

        $this->assertFalse($user->hasActiveContract());
    }
}
