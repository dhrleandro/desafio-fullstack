<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Contract;
use PHPUnit\Framework\TestCase;

class ContractTest extends TestCase
{
    public function test_create_returns_instance_with_default_values()
    {
        $planId = 5;
        $active = true;

        $contract = Contract::create($planId, $active);

        $this->assertNull($contract->id());
        $this->assertEquals($planId, $contract->planId());
        $this->assertTrue($contract->active());
        $this->assertNull($contract->createdAt());
    }

    public function test_from_array_returns_correct_instance()
    {
        $data = [
            'id' => 15,
            'plan_id' => 8,
            'active' => false,
            'created_at' => '2024-12-01 10:00:00 UTC',
        ];

        $contract = Contract::fromArray($data);

        $this->assertEquals($data['id'], $contract->id());
        $this->assertEquals($data['plan_id'], $contract->planId());
        $this->assertEquals($data['active'], $contract->active());
        $this->assertEquals($data['created_at'], $contract->createdAt()->toUtcTimeString());
    }

    public function test_to_array_returns_correct_array()
    {
        $data = [
            'id' => 15,
            'plan_id' => 8,
            'active' => true,
            'created_at' => '2024-12-01 10:00:00 UTC',
        ];

        $contract = Contract::fromArray($data);

        $this->assertEquals($data, $contract->toArray());
    }

    public function test_getters_returns_correct_values()
    {
        $data = [
            'id' => 15,
            'plan_id' => 8,
            'active' => true,
            'created_at' => '2024-12-01 10:00:00 UTC',
        ];

        $contract = Contract::fromArray($data);

        $this->assertEquals(15, $contract->id());
        $this->assertEquals(8, $contract->planId());
        $this->assertTrue($contract->active());
        $this->assertEquals('2024-12-01 10:00:00 UTC', $contract->createdAt()->toUtcTimeString());
    }
}
