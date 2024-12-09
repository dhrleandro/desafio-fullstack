<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeWrapper;


class Contract
{
    private ?int $id;
    private int $userId;
    private int $planId;
    private bool $active;
    private ?DateTimeWrapper $createdAt;
    private ?DateTimeWrapper $updatedAt;

    private function __construct(
        ?int $id,
        int $userId,
        int $planId,
        bool $active,
        ?DateTimeWrapper $createdAt = null,
        ?DateTimeWrapper $updatedAt = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->planId = $planId;
        $this->active = $active;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(int $userId, int $planId, bool $active): Contract
    {
        return new self(null,
            $userId,
            $planId,
            $active,
            null
        );
    }

    public static function fromArray(array $contract): Contract
    {
        return new self(
            $contract['id'] ?? null,
            $contract['user_id'],
            $contract['plan_id'],
            $contract['active'],
            isset($contract['created_at']) ? new DateTimeWrapper($contract['created_at']) : null,
            isset($contract['updated_at']) ? new DateTimeWrapper($contract['updated_at']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'plan_id' => $this->planId,
            'active' => $this->active,
            'created_at' => $this->createdAt?->toUtcTimeString() ?? '',
            'updated_at' => $this->updatedAt?->toUtcTimeString() ?? '',
        ];
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
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

    public function updatedAt(): ?DateTimeWrapper
    {
        return $this->updatedAt;
    }
}
