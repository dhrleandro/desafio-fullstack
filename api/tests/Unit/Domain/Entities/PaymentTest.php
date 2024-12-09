<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Payment;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use App\Domain\ValueObjects\PaymentStatus;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function test_create_returns_instance_with_default_values()
    {
        $contractId = 1;
        $planPrice = MonetaryValue::create(99.99);
        $dueDate = new DateTimeWrapper('2024-12-15T12:00:00.000000Z');

        $payment = Payment::create($contractId, $planPrice, $dueDate);
        $dueDate->setTime(0, 0, 0, 0);

        $this->assertNull($payment->id());
        $this->assertEquals($contractId, $payment->contractId());
        $this->assertEquals($planPrice->value(), $payment->planPrice()->value());
        $this->assertEquals(0, $payment->discount()->value());
        $this->assertEquals(0, $payment->amountCharged()->value());
        $this->assertEquals(0, $payment->creditRemaining()->value());
        $this->assertEquals($dueDate, $payment->dueDate());
        $this->assertEquals(PaymentStatus::PENDING, $payment->status());
    }

    public function test_from_array_returns_correct_instance()
    {
        $data = [
            'id' => 10,
            'contract_id' => 2,
            'plan_price' => 120.50,
            'discount' => 20.50,
            'amount_charged' => 100.00,
            'credit_remaining' => 10.00,
            'due_date' => '2024-12-15T00:00:00.000000Z',
            'status' => PaymentStatus::CONFIRMED->value,
            'created_at' => '2024-12-01T08:30:00.000000Z',
        ];

        $payment = Payment::fromArray($data);

        $this->assertEquals($data['id'], $payment->id());
        $this->assertEquals($data['contract_id'], $payment->contractId());
        $this->assertEquals($data['plan_price'], $payment->planPrice()->value());
        $this->assertEquals($data['discount'], $payment->discount()->value());
        $this->assertEquals($data['amount_charged'], $payment->amountCharged()->value());
        $this->assertEquals($data['credit_remaining'], $payment->creditRemaining()->value());
        $this->assertEquals($data['due_date'], $payment->dueDate()->toUtcTimeString());
        $this->assertEquals($data['status'], $payment->status()->value);
    }

    public function test_to_array_returns_correct_array()
    {
        $data = [
            'id' => 10,
            'contract_id' => 2,
            'plan_price' => 120.50,
            'discount' => 20.50,
            'amount_charged' => 100.00,
            'credit_remaining' => 10.00,
            'due_date' => '2024-12-15T00:00:00.000000Z',
            'status' => PaymentStatus::CONFIRMED->value,
            'created_at' => '2024-12-01T08:30:00.000000Z',
            'updated_at' => '2024-12-01T08:30:00.000000Z',
        ];

        $payment = Payment::fromArray($data);

        $this->assertEquals($data, $payment->toArray());
    }

    public function test_confirm_payment_updates_status_correctly()
    {
        $payment = Payment::create(
            1,
            MonetaryValue::create(100),
            new DateTimeWrapper('2024-12-15T12:00:00.000000Z')
        );

        $payment->confirmPayment();

        $this->assertEquals(PaymentStatus::CONFIRMED->value, $payment->status()->value);
    }
}
