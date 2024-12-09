<?php

namespace App\Repositories;

use App\Domain\Entities\Payment;

interface PaymentRepository
{
    public function create(Payment $payment): int;
    public function update(Payment $payment): bool;
    public function getById(int $id): ?Payment;
    /**
     * @param int $id
     * @return Payment[]
     */
    public function fetchByContractId(int $contract_id): array;
}