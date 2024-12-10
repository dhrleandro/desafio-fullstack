<?php

namespace App\Domain\Services;

use App\Domain\Entities\Contract;
use App\Domain\Entities\Payment;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Entities\ContractPayments;
use App\Domain\Services\Result\SwitchContractResult;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;

class SwitchContractService
{
    const EX_PLAN_NOT_FOUND = 'Plan not found';
    const EX_PLAN_IS_NOT_ACTIVE = 'Plan is not active';
    const EX_USER_NOT_HAS_ACTIVE_CONTRACT = 'The user does not have an active contract';
    const EX_USER_NOT_RELATED_TO_CURRENT_CONTRACT = 'User is not related to current contract';
    const EX_USER_ALREADY_HAVE_THIS_PLAN = 'User already have this plan';
    const EX_CONTRACT_NOT_FOUND = 'Contract not found';
    const EX_CONTRACT_IS_NOT_ACTIVE = 'Contract is not active';
    const EX_GET_LAST_CONFIRMED_PAYMENT = 'Error on get last confirmed payment';

    private function switchContractValidations(User $user, Contract $currentContract, Plan $newPlan): void
    {
        if (!$user->hasActiveContract()) {
            throw new \DomainException(self::EX_USER_NOT_HAS_ACTIVE_CONTRACT);
        }

        if ($currentContract->id() === null) {
            throw new \DomainException(self::EX_CONTRACT_NOT_FOUND);
        }

        if (!$currentContract->active()) {
            throw new \DomainException(self::EX_CONTRACT_IS_NOT_ACTIVE);
        }

        if ($currentContract->userId() !== $user->id() || $user->activeContractId() !== $currentContract->id()) {
            throw new \DomainException(self::EX_USER_NOT_RELATED_TO_CURRENT_CONTRACT);
        }

        if ($newPlan->id() === null) {
            throw new \DomainException(self::EX_PLAN_NOT_FOUND);
        }

        if (!$newPlan->active()) {
            throw new \DomainException(self::EX_PLAN_IS_NOT_ACTIVE);
        }

        if ($newPlan->id() === $currentContract->planId()) {
            throw new \DomainException(self::EX_USER_ALREADY_HAVE_THIS_PLAN);
        }
    }

    private function calculateNextDueDate(Payment $lastConfirmedPayment, int $monthDiffOfLastPaymentAndSwitchDate): DateTimeWrapper
    {
        $nextDueDate = $lastConfirmedPayment->dueDate()->getNextMonthWithSameDay();

        if ($monthDiffOfLastPaymentAndSwitchDate > 0) {
            $nextDueDate = $lastConfirmedPayment->dueDate()->getNextMonthWithSameDay($monthDiffOfLastPaymentAndSwitchDate);
        }

        return $nextDueDate;
    }

    private function calculateBeforeNextDueDate(Payment $lastConfirmedPayment, int $monthDiffOfLastPaymentAndSwitchDate): ?DateTimeWrapper
    {
        if ($monthDiffOfLastPaymentAndSwitchDate < 1)
        {
            return null;
        }

        return $lastConfirmedPayment->dueDate()->getNextMonthWithSameDay($monthDiffOfLastPaymentAndSwitchDate - 1);
    }

    private function daysOfBillingCycle(
        Payment $lastConfirmedPayment,
        ?DateTimeWrapper $beforeNextDueDate,
        DateTimeWrapper $nextDueDate): int
    {
        if ($beforeNextDueDate === null) {
            return $nextDueDate->differenceToWithTimeNormalized($lastConfirmedPayment->dueDate()->copyDate())->days;
        }

        return $nextDueDate->differenceToWithTimeNormalized($beforeNextDueDate->copyDate())->days;
    }

    /**
     * At the end of the expression, subtract 1 because the due date is the beginning of the new payment cycle
     * 
     * **Example:**
     * 
     * - Subscription plan: from 06/01/2024 to 07/01/2024  
     * - Billing cycle = 07/01/2024 - 06/01/2024 = 30 days
     * 
     * If the customer changed plans on 06/15/2024, then he used 15 days (half) and will have 15 days of credit.
     * 
     */
    private function calculateNonUsedDays(DateTimeWrapper $switchDate, DateTimeWrapper $nextDueDate): int
    {
        return abs($switchDate->differenceToWithTimeNormalized($nextDueDate->copyDate())->days) - 1;
    }

    private function calculateTotalCredits(
        MonetaryValue $lastPlanPrice,
        int $daysOfBillingCycle,
        int $nonUsedDays,
        MonetaryValue $recoveredCredit,): MonetaryValue
    {
        $pricePerDay = $lastPlanPrice->value() / $daysOfBillingCycle;
        $totalCredits = MonetaryValue::create($nonUsedDays * $pricePerDay);
        $totalCredits->sum($recoveredCredit->value());

        return $totalCredits;
    }

    /**
     * @return Payment[]
     */
    private function createPaymentsOfNewContract(
        DateTimeWrapper $switchDate,
        MonetaryValue $newPlanPrice,
        MonetaryValue $discount,
        MonetaryValue $amountCharged,
        MonetaryValue $creditRemaining): array
    {
        $fisrtPayment = Payment::create(null, $newPlanPrice, $switchDate);
        $fisrtPayment->setDiscount($discount);
        $fisrtPayment->setAmountCharged($amountCharged);
        $fisrtPayment->setCreditRemaining($creditRemaining);

        // simulate PIX payment confirmation
        $fisrtPayment->confirmPayment();

        // Creates future payments on the due date of the new plan until the remaining credits are zero
        $paymentDate = $fisrtPayment->dueDate()->copy();
        $newCreditRemaining = clone $fisrtPayment->creditRemaining();
        $futurePayments = [];
        while ($newCreditRemaining->value() > 0) {
            
            $paymentDate = $paymentDate->getNextMonthWithSameDayFromDate($paymentDate->copyDateImmutable());

            $newDiscount = $newPlanPrice->value() < $newCreditRemaining->value()
            ? $newPlanPrice
            : $newCreditRemaining;

            $newAmountCharged = MonetaryValue::create($newPlanPrice->value() - $newDiscount->value());
            
            $newCreditRemaining = $newCreditRemaining->value() >= $newDiscount->value()
                ? MonetaryValue::create($newCreditRemaining->value() - $newDiscount->value())
                : MonetaryValue::create(0);

            $newPayment = Payment::create(null, $newPlanPrice, $paymentDate);
            $newPayment->setDiscount($newDiscount);
            $newPayment->setAmountCharged($newAmountCharged);
            $newPayment->setCreditRemaining(MonetaryValue::create($newCreditRemaining->value()));

            $futurePayments[] = $newPayment;
        }

        return array_merge([$fisrtPayment], $futurePayments);
    }

    public function switchContract(
        User $user,
        ContractPayments $currentContract,
        Plan $newPlan,
        DateTimeWrapper $switchDate): SwitchContractResult
    {
        $this->switchContractValidations(
            $user,
            $currentContract->getContract(),
            $newPlan
        );

        $currentContract->cancelContract();
        $currentContract->confirmAllPaymentsBeforeDate($switchDate);
        $currentContract->cancelAllPendingPayments();

        $lastCurrentConfirmedPayment = $currentContract->getLastConfirmedPayment();
        if ($lastCurrentConfirmedPayment === null) {
            throw new \DomainException(message: self::EX_GET_LAST_CONFIRMED_PAYMENT);
        }

        $monthDiffOfLastPaymentAndSwitchDate = $switchDate->getNormalizedMonthDiff($lastCurrentConfirmedPayment->dueDate()->copyDateImmutable());

        $nextDueDate = $this->calculateNextDueDate(
            $lastCurrentConfirmedPayment,
            $monthDiffOfLastPaymentAndSwitchDate);
    
        $beforeNextDueDate = $this->calculateBeforeNextDueDate(
            $lastCurrentConfirmedPayment,
            $monthDiffOfLastPaymentAndSwitchDate);

        $daysOfBillingCycle = $this->daysOfBillingCycle(
            $lastCurrentConfirmedPayment,
            $beforeNextDueDate,
            $nextDueDate);

        $nonUsedDays = $this->calculateNonUsedDays($switchDate, $nextDueDate);
        $recoveredCredit = $lastCurrentConfirmedPayment->creditRemaining();

        $totalCredits = $this->calculateTotalCredits(
            $lastCurrentConfirmedPayment->planPrice(),
            $daysOfBillingCycle,
            $nonUsedDays,
            $recoveredCredit);

        $discount = $newPlan->price()->value() < $totalCredits->value()
            ? $newPlan->price()
            : $totalCredits;

        $amountCharged = MonetaryValue::create($newPlan->price()->value() - $discount->value());
        
        $creditRemaining = MonetaryValue::create(0);
        if ($totalCredits->value() >= $discount->value()) {
            $creditRemaining->sum($totalCredits->value() - $discount->value());
        }

        $newContractPayments = $this->createPaymentsOfNewContract(
            $switchDate,
            $newPlan->price(),
            $discount,
            $amountCharged,
            $creditRemaining);

        $newContract = Contract::create($user->id(), $newPlan->id(), true);
        $response = new SwitchContractResult();
        $response->currentContract = $currentContract;
        $response->newContract = ContractPayments::create($newContract, $newContractPayments);
        return $response;
    }
}