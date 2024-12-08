<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use App\Domain\ValueObjects\PaymentStatus;


class Payment
{
    private ?int $id;
    private int $contractId;
    private MonetaryValue $planPrice;
    private MonetaryValue $discount;
    private MonetaryValue $amountCharged;
    private MonetaryValue $creditRemaining;
    private ?DateTimeWrapper $dueDate;
    private PaymentStatus $status;
    private ?DateTimeWrapper $createdAt;

    private function __construct(
        ?int $id,
        int $contractId,
        MonetaryValue $planPrice,
        MonetaryValue $discount,
        MonetaryValue $amountCharged,
        MonetaryValue $creditRemaining,
        ?DateTimeWrapper $dueDate,
        PaymentStatus $status = PaymentStatus::PENDING,
        ?DateTimeWrapper $createdAt = null)
    {
        $this->id = $id;
        $this->contractId = $contractId;
        $this->planPrice = $planPrice;
        $this->discount = $discount;
        $this->amountCharged = $amountCharged;
        $this->creditRemaining = $creditRemaining;
        $this->dueDate = $dueDate;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public static function create(
        int $contractId,
        MonetaryValue $planPrice): Payment
    {
        $id = null;
        $discount = MonetaryValue::create(0);
        $amountCharged = MonetaryValue::create(0);
        $creditRemaining = MonetaryValue::create(0);
        $dueDate = null;
        $status = PaymentStatus::PENDING;
        $createdAt = null;

        return new self($id,
            $contractId,
            $planPrice,
            $discount,
            $amountCharged,
            $creditRemaining,
            $dueDate,
            $status,
            $createdAt
        );
    }

    public static function fromArray(array $payment): Payment
    {
        $id = $payment['id'] ?? null;
        $contractId = $payment['contract_id'];
        $planPrice = MonetaryValue::create($payment['plan_price']);
        $discount = MonetaryValue::create($payment['discount']);
        $amountCharged =  MonetaryValue::create($payment['amount_charged']);
        $creditRemaining = MonetaryValue::create($payment['credit_remaining']);
        $dueDate = $payment['due_date'] ? new DateTimeWrapper($payment['due_date']) : null;
        $status = $payment['status'];
        $createdAt = $payment['created_at'] ? new DateTimeWrapper($payment['created_at']) : null;

        return new self($id,
            $contractId,
            $planPrice,
            $discount,
            $amountCharged,
            $creditRemaining,
            $dueDate,
            $status,
            $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contract_id' => $this->contractId,
            'plan_price' => $this->planPrice->value(),
            'discount' => $this->discount->value(),
            'amount_charged' => $this->amountCharged->value(),
            'credit_remaining' => $this->creditRemaining->value(),
            'due_date' => $this->dueDate?->toUtcTimeString() ?? '',
            'status' => $this->status,
            'created_at' => $this->createdAt?->toUtcTimeString() ?? ''
        ];
    }

    public function confirmPayment(): void
    {
        $this->status = PaymentStatus::CONFIRMED;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function contractId(): int
    {
        return $this->contractId;
    }

    public function planPrice(): MonetaryValue
    {
        return $this->planPrice;
    }

    public function discount(): MonetaryValue
    {
        return $this->discount;
    }

    public function amountCharged(): MonetaryValue
    {
        return $this->amountCharged;
    }

    public function creditRemaining(): MonetaryValue
    {
        return $this->creditRemaining;
    }

    public function dueDate(): ?DateTimeWrapper
    {
        return $this->dueDate;
    }

    public function status(): PaymentStatus
    {
        return $this->status;
    }

    public function createdAt(): ?DateTimeWrapper
    {
        return $this->createdAt;
    }
}
