<?php

namespace App\Domain\Services;

use App\Domain\Entities\Contract;
use App\Domain\Entities\Payment;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Entities\ContractPayments;
use App\Domain\ValueObjects\DateTimeWrapper;

class ContractPaymentDomainService
{
    private function checkUser(User $user): void
    {
        if (!$user->id()) {
            throw new \DomainException("User not found");
        }
    }

    private function checkPlan(Plan $plan): void
    {
        if ($plan->id() === null) {
            throw new \DomainException("Plan not found");
        }

        if (!$plan->active()) {
            throw new \DomainException("Plan is not active");
        }
    }

    public function contractPlan(User $user, Plan $plan, DateTimeWrapper $firstPaymentDueDate): ContractPayments
    {
        $this->checkUser($user);

        if ($user->hasActiveContract()) {
            throw new \DomainException("The user already has an active contract");
        }

        $this->checkPlan($plan);

        $contract = Contract::create($user->id(), $plan->id(), true);
        $payment = Payment::create(
            $contract->id(),
            $plan->price(),
            $firstPaymentDueDate->copy()
        );

        return ContractPayments::create($contract, [$payment]);
    }

    public function switchPlan(User $user, Contract $contract, Plan $plan): void
    {
        $this->checkUser($user);
        $this->checkPlan($plan);

        if ($user->id() !== $contract->userId()) {
            throw new \DomainException("The user does not have this contract");
        }

        if ($contract->id() === null) {
            throw new \DomainException("Contract not found");
        }

        if (!$contract->active()) {
            throw new \DomainException("Contract is not active");
        }
    }
}