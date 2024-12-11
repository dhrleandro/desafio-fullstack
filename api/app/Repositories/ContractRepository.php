<?php

namespace App\Repositories;

use App\Domain\Entities\Contract;

interface ContractRepository
{
    public function create(Contract $contract): int;
    public function update(Contract $contract): bool;
    public function getById(int $id): ?Contract;
    /**
     * @param int $id
     * @return Contract[]
     */
    public function fetchAll(int $userId): array;
    /**
     * @param int $id
     * @return Contract[]
     */
    public function fetchActiveContracts(int $id): array;
}