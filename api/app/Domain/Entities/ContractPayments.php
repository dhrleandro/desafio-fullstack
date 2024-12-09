<?php

namespace App\Domain\Entities;

use App\Domain\Entities\Contract;
use App\Domain\Entities\Payment;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\PaymentStatus;

class ContractPayments
{
    private Contract $contract;

    /** @var Payment[] */
    private array $payments;

    /**
     * @param Contract $contract
     * @param Payment[] $payment
     */
    private function __construct(
        Contract $contract,
        ?array $payments
    ) {
        $this->contract = $contract;
        $this->payments = $payments ?? [];
    }

    /**
     * @param Contract $contract
     * @param Payment[] $payment
     */
    public static function create(
        Contract $contract,
        ?array $payments
    ): ContractPayments
    {
        return new self(
            $contract,
            $payments ? self::clonePayments($payments) : []
        );
    }

    /**
     * @param Payment[] $paymentsArray
     * @return Payment[]
     */
    private static function clonePayments(array $paymentsArray): array
    {
        return array_map(fn (Payment $payment) => clone $payment, $paymentsArray);
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->clonePayments($this->payments);
    }

    public function confirmAllPaymentsBeforeDate(DateTimeWrapper $date): void
    {
        foreach ($this->payments as $key => $payment) {

            if ($payment->status() !== PaymentStatus::PENDING) {
                continue;
            }

            $dueDate = $payment->dueDate();
            if ($dueDate->isAfter($date)) {
                continue;
            }

            $payment->confirmPayment();
            $this->payments[$key] = clone $payment;
        }
    }

    public function cancelAllPendingPayments(): void
    {
        foreach ($this->payments as $key => $payment) {

            if ($payment->status() !== PaymentStatus::PENDING) {
                continue;
            }

            $payment->cancelPayment();
            $this->payments[$key] = clone $payment;
        }
    }

    public function getLastConfirmedPayment(): ?Payment
    {
        $paymentsCopy = $this->getPayments();
        
        // sort payments by descending due date
        usort($paymentsCopy, function (Payment $a, Payment $b) {
            $dateA = $a->dueDate()->copyDateImmutable();
            $dateB = $b->dueDate()->copyDateImmutable();

            if ($dateA === $dateB) {
                throw new \DomainException("Payments have the same due date");
            }

            return $dateA < $dateB ? 1 : -1;
        });

        foreach ($paymentsCopy as $payment) {
            if ($payment->status() == PaymentStatus::CONFIRMED) {
                return clone $payment;
            }
        }

        return null;
    }
}
