<?php

namespace App\Repositories\Implementations;

use App\Domain\Entities\Contract;
use App\Models\Contract as EloquentContract;
use App\Repositories\ContractRepository;

class EloquentContractRepository implements ContractRepository
{
    public function create(Contract $contract): int
    {
        $eloquentContract = EloquentContract::create($contract->toArray());
        return $eloquentContract->id;
    }
    public function update(Contract $contract): bool
    {
        $eloquentContract = EloquentContract::find($contract->id());
        if (!$eloquentContract) {
            return false;
        }

        return $eloquentContract->update($contract->toArray());
    }

    public function getById(int $id): ?Contract
    {
        $eloquentContract = EloquentContract::find($id);
        if (!$eloquentContract) {
            return null;
        }

        return Contract::fromArray($eloquentContract->toArray());
    }

    /**
     * @param int $id
     * @return Contract[]
     */
    public function fetchAll(int $userId): array
    {
        $eloquentContracts = EloquentContract::where('user_id', '=', $userId)->get();
        $contracts = [];

        foreach ($eloquentContracts as $eloquentContract) {
            $contracts[] = Contract::fromArray($eloquentContract->toArray());
        }

        return $contracts;
    }

    /**
     * @param int $id
     * @return Contract[]
     */
    public function fetchActiveContracts(int $user_id): array
    {
        $eloquentContracts = EloquentContract::where([
            ['user_id', '=', $user_id],
            ['active', '=', true],
        ])->get();

        $contracts = [];
        foreach ($eloquentContracts as $eloquentContract) {
            $contracts[] = Contract::fromArray($eloquentContract->toArray());
        }

        return $contracts;
    }
}
