<?php

namespace App\Domain\Entities;


class User
{
    private int $id;
    private ?int $activeContractId;

    private function __construct(
        int $id,
        ?int $activeContractId = null)
    {
        $this->id = $id;
        $this->activeContractId = $activeContractId;
    }

    public static function create(int $id, ?int $activeContractId = null): User
    {
        return new self(
            $id,
            $activeContractId
        );
    }

    public static function fromArray(array $user): User
    {
        return new self(
            $user['id'],
            $user['active_contract_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'active_contract_id' => $this->activeContractId ?? '',
        ];
    }

    public function hasActiveContract(): bool
    {
        return $this->activeContractId !== null;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function activeContractId(): ?int
    {
        return $this->activeContractId;
    }
}
