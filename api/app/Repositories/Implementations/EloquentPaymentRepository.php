<?php

namespace App\Repositories\Implementations;

use App\Repositories\PaymentRepository;
use App\Domain\Entities\Payment;
use App\Models\Payment as EloquentPayment;

class EloquentPaymentRepository implements PaymentRepository
{
    public function create(Payment $payment): int
    {
        $eloquentPayment = EloquentPayment::create($payment->toArray());
        return $eloquentPayment->id;
    }

    public function update(Payment $payment): bool
    {
        $eloquentPayment = EloquentPayment::find($payment->id());
        if (!$eloquentPayment) {
            return false;
        }

        return $eloquentPayment->update($payment->toArray());
    }

    public function getById(int $id): ?Payment
    {
        $eloquentPayment = EloquentPayment::find($id);
        if (!$eloquentPayment) {
            return null;
        }

        return Payment::fromArray($eloquentPayment->toArray());
    }

    /**
     * @param int $id
     * @return Payment[]
     */
    public function fetchByContractId(int $contractId): array
    {
        $eloquentPayments = EloquentPayment::where('contract_id', '=', $contractId)->get();

        $payments = [];
        foreach ($eloquentPayments as $eloquentPayment) {
            $payments[] = Payment::fromArray($eloquentPayment->toArray());
        }

        return $payments;
    }
}
