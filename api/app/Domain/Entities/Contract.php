<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeWrapper;


class Contract
{
    private ?int $id;
    private int $planId;
    private bool $active;
    private ?DateTimeWrapper $createdAt;

    private function __construct(
        ?int $id,
        int $planId,
        bool $active,
        ?DateTimeWrapper $createdAt = null)
    {
        $this->id = $id;
        $this->planId = $planId;
        $this->active = $active;
        $this->createdAt = $createdAt;
    }

    public static function create(int $planId, bool $active): Contract
    {
        return new self(null,
            $planId,
            $active,
            null
        );
    }

    public static function fromArray(array $contract): Contract
    {
        return new self(
            $contract['id'] ?? null,
            $contract['plan_id'],
            $contract['active'],
            new DateTimeWrapper($contract['created_at']) ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->planId,
            'active' => $this->active,
            'created_at' => $this->createdAt?->toUtcTimeString() ?? ''
        ];
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function planId(): int
    {
        return $this->planId;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function createdAt(): ?DateTimeWrapper
    {
        return $this->createdAt;
    }
}
