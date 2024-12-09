<?php

namespace Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Contract as EloquentContract;
use App\Models\Payment as EloquentPayment;
use App\Repositories\Implementations\EloquentPaymentRepository;
use App\Domain\Entities\Contract;
use App\Domain\Entities\Payment;
use App\Domain\ValueObjects\PaymentStatus;
use Tests\TestCase;

class EloquentPaymentRepositoryTest extends TestCase
{
    /** 
     * Note: `use RefreshDatabase;` causes errors in parallel tests. 
     * DatabaseMigrations is slower but works.
     */
    use DatabaseMigrations;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->repository = new EloquentPaymentRepository();

        $contract = Contract::create(1, 1, true);
        EloquentContract::create($contract->toArray());

        $contract = Contract::create(1, 2, true);
        EloquentContract::create($contract->toArray());
    }

    public function test_it_creates_a_payment()
    {
        $expectedDueDate = "2024-12-09 12:17:10+00";
        $payment = Payment::fromArray([
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 100,
            'amount_charged' => 100,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING->value
        ]);

        $paymentId = $this->repository->create($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $paymentId,
            'contract_id' => 1,
            'plan_price' => '1000.00',
            'discount' => 100,
            'amount_charged' => 100,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING,
        ]);
    }

    public function test_it_updates_a_payment()
    {
        $expectedDueDate = "2024-12-09 12:17:10+00";
        $payment = EloquentPayment::create([
            'contract_id' => 1,
            'plan_price' => 1000,
            'status' => PaymentStatus::PENDING->value,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate
        ]);

        $updatedPayment = Payment::fromArray([
            'id' => $payment->id,
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 100,
            'amount_charged' => 100,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::CONFIRMED->value
        ]);

        $result = $this->repository->update($updatedPayment);

        $this->assertTrue($result);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'contract_id' => 1,
            'plan_price' => '1000.00',
            'discount' => 100,
            'amount_charged' => 100,
            'credit_remaining'=> 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::CONFIRMED
        ]);
    }

    public function test_it_throws_exception_when_payment_not_found_on_update()
    {
        $expectedDueDate = "2024-12-09 12:17:10+00";
        $payment = Payment::fromArray([
            'id' => 9999,
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING->value
        ]);

        $result = $this->repository->update($payment);

        $this->assertFalse($result);
    }

    public function test_it_fetches_a_payment_by_id()
    {
        $expectedDueDate = "2024-12-09 12:17:10+00";
        $eloquentPayment = EloquentPayment::create([
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        $payment = $this->repository->getById($eloquentPayment->id);

        $this->assertNotNull($payment);
        $this->assertEquals($eloquentPayment->id, $payment->id());
    }

    public function test_it_returns_null_if_payment_not_found_by_id()
    {
        $payment = $this->repository->getById(9999);

        $this->assertNull($payment);
    }

    public function test_it_fetches_payments_by_contract_id()
    {
        $expectedDueDate = "2024-12-09 12:17:10+00";
        EloquentPayment::create([
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        EloquentPayment::create([
            'contract_id' => 1,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        EloquentPayment::create([
            'contract_id' => 2,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        EloquentPayment::create([
            'contract_id' => 2,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        EloquentPayment::create([
            'contract_id' => 2,
            'plan_price' => 1000,
            'discount' => 0,
            'amount_charged' => 0,
            'credit_remaining' => 0,
            'due_date' => $expectedDueDate,
            'status' => PaymentStatus::PENDING
        ]);

        $payments = $this->repository->fetchByContractId(1);
        $this->assertCount(2, $payments);

        $payments = $this->repository->fetchByContractId(2);
        $this->assertCount(3, $payments);
    }
}
