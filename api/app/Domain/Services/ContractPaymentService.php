<?php

namespace App\Domain\Services;

use App\Domain\Entities\Contract;
use App\Domain\Entities\Payment;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Entities\ContractPayments;
use App\Domain\ValueObjects\DateTimeWrapper;

class ContractPaymentService
{
    const EX_PLAN_NOT_FOUND = 'Plan not found';
    const EX_PLAN_IS_NOT_ACTIVE = 'Plan is not active';
    const EX_USER_HAS_ACTIVE_CONTRACT = 'The user already has an active contract';

    private function checkPlan(Plan $plan): void
    {
        if ($plan->id() === null) {
            throw new \DomainException(self::EX_PLAN_NOT_FOUND);
        }

        if (!$plan->active()) {
            throw new \DomainException(self::EX_PLAN_IS_NOT_ACTIVE);
        }
    }

    public function createContract(User $user, Plan $plan, DateTimeWrapper $firstPaymentDueDate): ContractPayments
    {
        if ($user->hasActiveContract()) {
            throw new \DomainException(self::EX_USER_HAS_ACTIVE_CONTRACT);
        }

        $this->checkPlan($plan);

        $contract = Contract::create($user->id(), $plan->id(), true);
        $payment = Payment::create(
            $contract->id(),
            $plan->price(),
            $firstPaymentDueDate->copy()
        );
        $payment->setAmountCharged($plan->price());
        
        // simulate PIX payment confirmation
        $payment->confirmPayment();

        return ContractPayments::create($contract, [$payment]);
    }
}