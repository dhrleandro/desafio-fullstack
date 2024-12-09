<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Plan;
use App\Domain\ValueObjects\MonetaryValue;
use PHPUnit\Framework\TestCase;

class PlanTest extends TestCase
{
    public function test_create_returns_instance_with_default_values()
    {
        $description = 'Premium Plan';
        $numberOfClients = 100;
        $gigabytesStorage = 500;
        $price = MonetaryValue::create(99.99);
        $active = true;

        $plan = Plan::create($description, $numberOfClients, $gigabytesStorage, $price, $active);

        $this->assertNull($plan->id());
        $this->assertEquals($description, $plan->description());
        $this->assertEquals($numberOfClients, $plan->numberOfClients());
        $this->assertEquals($gigabytesStorage, $plan->gigabytesStorage());
        $this->assertEquals($price->value(), $plan->price()->value());
        $this->assertTrue($plan->active());
        $this->assertNull($plan->createdAt());
        $this->assertNull($plan->updatedAt());
    }

    public function test_from_array_returns_correct_instance()
    {
        $data = [
            'id' => 20,
            'description' => 'Enterprise Plan',
            'number_of_clients' => 500,
            'gigabytes_storage' => 2000,
            'price' => 499.99,
            'active' => true,
            'created_at' => '2024-12-01 12:00:00 UTC',
            'updated_at' => '2024-12-05 14:00:00 UTC',
        ];

        $plan = Plan::fromArray($data);

        $this->assertEquals($data['id'], $plan->id());
        $this->assertEquals($data['description'], $plan->description());
        $this->assertEquals($data['number_of_clients'], $plan->numberOfClients());
        $this->assertEquals($data['gigabytes_storage'], $plan->gigabytesStorage());
        $this->assertEquals($data['price'], $plan->price()->value());
        $this->assertEquals($data['active'], $plan->active());
        $this->assertEquals($data['created_at'], $plan->createdAt()->toUtcTimeString());
        $this->assertEquals($data['updated_at'], $plan->updatedAt()->toUtcTimeString());
    }

    public function test_to_array_returns_correct_array()
    {
        $data = [
            'id' => 20,
            'description' => 'Enterprise Plan',
            'number_of_clients' => 500,
            'gigabytes_storage' => 2000,
            'price' => 499.99,
            'active' => true,
            'created_at' => '2024-12-01 12:00:00 UTC',
            'updated_at' => '2024-12-05 14:00:00 UTC',
        ];

        $plan = Plan::fromArray($data);

        $this->assertEquals($data, $plan->toArray());
    }

    public function test_getters_returns_correct_values()
    {
        $data = [
            'id' => 20,
            'description' => 'Enterprise Plan',
            'number_of_clients' => 500,
            'gigabytes_storage' => 2000,
            'price' => 499.99,
            'active' => true,
            'created_at' => '2024-12-01 12:00:00 UTC',
            'updated_at' => '2024-12-05 14:00:00 UTC',
        ];

        $plan = Plan::fromArray($data);

        $this->assertEquals(20, $plan->id());
        $this->assertEquals('Enterprise Plan', $plan->description());
        $this->assertEquals(500, $plan->numberOfClients());
        $this->assertEquals(2000, $plan->gigabytesStorage());
        $this->assertEquals(499.99, $plan->price()->value());
        $this->assertTrue($plan->active());
        $this->assertEquals('2024-12-01 12:00:00 UTC', $plan->createdAt()->toUtcTimeString());
        $this->assertEquals('2024-12-05 14:00:00 UTC', $plan->updatedAt()->toUtcTimeString());
    }
}
