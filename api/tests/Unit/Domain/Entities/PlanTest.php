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
            'created_at' => '2024-12-01T12:00:00.000000Z',
            'updated_at' => '2024-12-05T14:00:00.000000Z',
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
            'created_at' => '2024-12-01T12:00:00.000000Z',
            'updated_at' => '2024-12-05T14:00:00.000000Z',
        ];

        $plan = Plan::fromArray($data);

        $this->assertEquals($data, $plan->toArray());
    }

    public function test_set_id_sets_correct_id()
    {
        $id = 20;
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            false
        );
        $plan->setId($id);

        $this->assertEquals($id, $plan->id());
    }
}
