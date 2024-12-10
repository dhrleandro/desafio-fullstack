<?php

namespace App\Domain\Services\Result;

use App\Domain\Entities\ContractPayments;

class SwitchContractResult
{
    public ContractPayments $currentContract;
    public ContractPayments $newContract;
}