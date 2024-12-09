<?php

namespace App\Domain\Entities;


use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;

class Plan
{
    private ?int $id;
    private string $description;
    private int $numberOfClients;
    private int $gigabytesStorage;
    private MonetaryValue $price;
    private bool $active;
    private ?DateTimeWrapper $createdAt;
    private ?DateTimeWrapper $updatedAt;

    private function __construct(
        ?int $id,
        string $description,
        int $numberOfClients,
        int $gigabytesStorage,
        MonetaryValue $price,
        bool $active = true,
        ?DateTimeWrapper $createdAt = null,
        ?DateTimeWrapper $updatedAt = null
    )
    {
        $this->id = $id;
        $this->description = $description;
        $this->numberOfClients = $numberOfClients;
        $this->gigabytesStorage = $gigabytesStorage;
        $this->price = $price;
        $this->active = $active;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        string $description,
        int $numberOfClients,
        int $gigabytesStorage,
        MonetaryValue $price,
        bool $active = true
    )
    {
        return new self(null,
            $description,
            $numberOfClients,
            $gigabytesStorage,
            $price,
            $active
        );
    }

    public static function fromArray(array $plan): Plan
    {
        $id = $plan["id"] ?? null;
        $description = $plan["description"];
        $numberOfClients = $plan["number_of_clients"];
        $gigabytesStorage = $plan["gigabytes_storage"];
        $price = MonetaryValue::create($plan["price"]);
        $active = $plan["active"];
        $createdAt = isset($plan["created_at"]) ? new DateTimeWrapper($plan["created_at"]) : null;
        $updatedAt = isset($plan["updated_at"]) ? new DateTimeWrapper($plan["updated_at"]) : null;
        
        return new self(
            $id,
            $description,
            $numberOfClients,
            $gigabytesStorage,
            $price,
            $active,
            $createdAt,
            $updatedAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'number_of_clients' => $this->numberOfClients,
            'gigabytes_storage' => $this->gigabytesStorage,
            'price' => $this->price->value(),
            'active' => $this->active,
            'created_at' => $this->createdAt?->toUtcTimeString() ?? '',
            'updated_at' => $this->updatedAt?->toUtcTimeString() ?? ''
        ];
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function numberOfClients(): int
    {
        return $this->numberOfClients;
    }

    public function gigabytesStorage(): int
    {
        return $this->gigabytesStorage;
    }

    public function price(): MonetaryValue
    {
        return $this->price;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function createdAt(): ?DateTimeWrapper
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeWrapper
    {
        return $this->updatedAt;
    }
}