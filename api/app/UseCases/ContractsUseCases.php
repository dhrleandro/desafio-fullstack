<?php

namespace App\UseCases;

use \App\Domain\Entities\Contract;
use App\Repositories\ContractRepository;

class ContractsUseCases
{
    private ContractRepository $contractRepository;

    public function __construct(ContractRepository $contractRepository)
    {
        $this->contractRepository = $contractRepository;
    }

    /**
     * @return Contract[]
     */
    public function getAllContracts(): array {
        return $this->contractRepository->fetchAll();
    }
}