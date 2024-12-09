<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Contract;
use PHPUnit\Framework\TestCase;

class ContractTest extends TestCase
{
    public function test_create_returns_instance_with_default_values()
    {
        $userId = 1;
        $planId = 5;
        $active = true;

        $contract = Contract::create($userId, $planId, $active);

        $this->assertNull($contract->id());
        $this->assertEquals($userId, $contract->userId());
        $this->assertEquals($planId, $contract->planId());
        $this->assertTrue($contract->active());
        $this->assertNull($contract->createdAt());
    }

    public function test_from_array_returns_correct_instance()
    {
        $data = [
            'id' => 15,
            'user_id' => 1,
            'plan_id' => 8,
            'active' => false,
            'created_at' => '2024-12-01T10:00:00.000000Z',
            'updated_at' => '2024-12-01T10:00:00.000000Z',
        ];

        $contract = Contract::fromArray($data);

        $this->assertEquals($data['id'], $contract->id());
        $this->assertEquals($data['user_id'], $contract->userId());
        $this->assertEquals($data['plan_id'], $contract->planId());
        $this->assertEquals($data['active'], $contract->active());
        $this->assertEquals($data['created_at'], $contract->createdAt()?->toUtcTimeString());
        $this->assertEquals($data['updated_at'], $contract->updatedAt()?->toUtcTimeString());
    }

    public function test_to_array_returns_correct_array()
    {
        $data = [
            'id' => 15,
            'user_id' => 1,
            'plan_id' => 8,
            'active' => true,
            'created_at' => '2024-12-01T10:00:00.000000Z',
            'updated_at' => '2024-12-01T10:00:00.000000Z'
        ];

        $contract = Contract::fromArray($data);

        $this->assertEquals($data, $contract->toArray());
    }
}
