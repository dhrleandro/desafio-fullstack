<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Contract;
use App\Domain\Entities\ContractPayments;
use App\Domain\Entities\Payment;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use App\Domain\ValueObjects\PaymentStatus;
use PHPUnit\Framework\TestCase;

class ContractPaymentsTest extends TestCase
{
    /**
     * @return Payment[]
     */
    private function createPaymentList(): array
    {
        return [
            0 => Payment::create(
                1,
                MonetaryValue::create(100),
                new DateTimeWrapper('2024-01-01 12:00:00 UTC')
            ),

            1 => Payment::create(
                1,
                MonetaryValue::create(200),
                new DateTimeWrapper('2024-01-15 10:05:00 UTC')
            ),

            2 => Payment::create(
                1,
                MonetaryValue::create(200),
                new DateTimeWrapper('2024-02-15 20:15:23 UTC')
            ),

            3 => Payment::create(
                1,
                MonetaryValue::create(200),
                new DateTimeWrapper('2024-03-15 17:00:00 UTC')
            ),

            4 => Payment::create(
                1,
                MonetaryValue::create(200),
                new DateTimeWrapper('2024-04-15 09:00:00 UTC')
            ),

            5 => Payment::create(
                1,
                MonetaryValue::create(200),
                new DateTimeWrapper('2024-05-15 19:00:00 UTC')
            )
        ];
    }

    public function test_get_contract_returns_correct_contract()
    {
        $contract = Contract::create(15, 30, true);
        $contractPayments = ContractPayments::create($contract, $this->createPaymentList());

        $this->assertEquals($contract, $contractPayments->getContract());
    }

    public function test_get_payments_returns_correct_payments()
    {
        $contract = Contract::create(15, 30, true);
        $payments = $this->createPaymentList();
        $contractPayments = ContractPayments::create($contract, $payments);

        foreach ($payments as $key => $payment) {
            $this->assertEquals($payments[$key], $contractPayments->getPayments()[$key]);
        }
    }

    public function test_create_returns_instance_with_default_values()
    {
        $contract = Contract::create(1, 1, true);
        $payments = [
            Payment::create(1,MonetaryValue::create(100), new DateTimeWrapper('2024-01-15T12:00:00.000000Z')),
            Payment::create(1,MonetaryValue::create(200), new DateTimeWrapper('2024-06-15T12:00:00.000000Z')),
            Payment::create(1,MonetaryValue::create(300), new DateTimeWrapper('2024-07-15T12:00:00.000000Z')),
            Payment::create(1,MonetaryValue::create(400), new DateTimeWrapper('2024-12-15T12:00:00.000000Z')),
        ];

        $contractPayments = ContractPayments::create($contract, $payments);

        $this->assertEquals($contract, $contractPayments->getContract());
        $this->assertEquals($payments, $contractPayments->getPayments());
    }

    public function test_confirm_all_payments_before_date_should_confirm_payments_correctly()
    {
        $contract = Contract::create(1, 1, true);
        $changePlanDate = new DateTimeWrapper('2024-03-05 18:45:00 UTC');
        $payments = $this->createPaymentList();

        $expectedStatuses = [
            0 => PaymentStatus::CONFIRMED,
            1 => PaymentStatus::CONFIRMED,
            2 => PaymentStatus::CONFIRMED,
            3 => PaymentStatus::PENDING,
            4 => PaymentStatus::PENDING,
            5 => PaymentStatus::PENDING,
        ];

        $contractPayments = ContractPayments::create($contract, $payments);
        $contractPayments->confirmAllPaymentsBeforeDate($changePlanDate);

        foreach ($payments as $key => $payment) {
            $this->assertEquals(
                $payments[$key]->dueDate()->toUTCTimeString(),
                $contractPayments->getPayments()[$key]->dueDate()->toUTCTimeString(),
            );

            $this->assertEquals($expectedStatuses[$key], $contractPayments->getPayments()[$key]->status());
        }
    }

    public function test_cancel_all_pending_payments_should_cancel_payments_correctly()
    {
        $contract = Contract::create(1, 1, true);
        $payments = $this->createPaymentList();

        $payments[0]->confirmPayment();
        $payments[1]->confirmPayment();
        $payments[2]->confirmPayment();

        $expectedStatuses = [
            0 => PaymentStatus::CONFIRMED,
            1 => PaymentStatus::CONFIRMED,
            2 => PaymentStatus::CONFIRMED,
            3 => PaymentStatus::CANCELED,
            4 => PaymentStatus::CANCELED,
            5 => PaymentStatus::CANCELED,
        ];

        $contractPayments = ContractPayments::create($contract, $payments);
        $contractPayments->cancelAllPendingPayments();

        foreach ($payments as $key => $payment) {
            $this->assertEquals(
                $payments[$key]->dueDate()->toUTCTimeString(),
                $contractPayments->getPayments()[$key]->dueDate()->toUTCTimeString(),
            );

            $this->assertEquals($expectedStatuses[$key], $contractPayments->getPayments()[$key]->status());
        }
    }

    public function test_get_last_confirmed_payment_should_return_last_confirmed_payment()
    {
        $contract = Contract::create(1, 1, true);
        $payments = $this->createPaymentList();

        $expected = $payments[1]->toArray();

        $payments[0]->confirmPayment();
        $payments[1]->confirmPayment();
        $expected['status'] = PaymentStatus::CONFIRMED->value;

        $contractPayments = ContractPayments::create($contract, $payments);
        $lastConfirmedPayment = $contractPayments->getLastConfirmedPayment();

        $this->assertEquals($expected, $lastConfirmedPayment->toArray());
    }
}
