<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Services\SwitchContractService;
use App\Domain\Entities\Contract;
use App\Domain\Entities\ContractPayments;
use App\Domain\Entities\Payment;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use App\Domain\ValueObjects\PaymentStatus;

trait SwitchContractServiceTestTrait
{
    private function SwitchContractService(): SwitchContractService
    {
        return new SwitchContractService();
    }

    private function switchContractSimpleTestInputs(): array
    {
        $userId = 78;
        $currentPlanId = 100;
        $currentContractId = 1;

        $currentPlan = Plan::create(
            'A Plan',
            100,
            100,
            MonetaryValue::create(100),
            true
        );
        $currentPlan->setId($currentPlanId);

        $currentContract = Contract::create($userId, $currentPlanId, true);
        $currentContract->setId(1);

        $currentPayments = [
            Payment::fromArray([
                'id' => 1,
                'contract_id' => $currentContractId,
                'plan_price' => 100,
                'discount' => 0,
                'amount_charged' => 100,
                'credit_remaining' => 0,
                'due_date' => '2024-01-01 00:00:00 UTC',
                'status' => PaymentStatus::CONFIRMED->value,
                'created_at' => '2024-01-01 00:00:00 UTC',
                'updated_at' => '2024-01-01 00:00:00 UTC',
            ])
        ];

        $currentContractPayments = ContractPayments::create($currentContract, $currentPayments);
        $user = User::create($userId, $currentContract->id());

        $commands = [];
        $commands[0] = [
            'switch_date' => DateTimeWrapper::create('2024-01-15 03:00:00 UTC'),
            'new_plan' => Plan::create(
                'B Plan',
                200,
                200,
                MonetaryValue::create(200),
                true
            )
        ];
        $commands[0]['new_plan']->setId(200);

        $commands[1] = [
            'switch_date' => DateTimeWrapper::create('2024-05-20 03:00:00 UTC'),
            'new_plan' => Plan::create(
                'C Plan',
                400,
                400,
                MonetaryValue::create(400),
                true
            )
        ];
        $commands[1]['new_plan']->setId(400);

        return [
            'user' => $user,
            'current_contract_payments' => $currentContractPayments,
            'current_plan' => $currentPlan,
            'switch_contract_commands' => $commands
        ];
    }

    private function switchContractComplexTestInputs(): array
    {
        $userId = 78;
        $currentPlanId = 800;
        $currentContractId = 1;

        $currentPlan = Plan::create(
            '800 Plan',
            800,
            800,
            MonetaryValue::create(800),
            true
        );
        $currentPlan->setId($currentPlanId);

        $currentContract = Contract::create($userId, $currentPlanId, true);
        $currentContract->setId(1);

        $currentPayments = [
            Payment::fromArray([
                'id' => 1,
                'contract_id' => $currentContractId,
                'plan_price' => 800,
                'discount' => 0,
                'amount_charged' => 800,
                'credit_remaining' => 0,
                'due_date' => '2024-01-01 00:00:00 UTC',
                'status' => PaymentStatus::CONFIRMED->value,
                'created_at' => '2024-01-01 00:00:00 UTC',
                'updated_at' => '2024-01-01 00:00:00 UTC',
            ])
        ];

        $currentContractPayments = ContractPayments::create($currentContract, $currentPayments);
        $user = User::create($userId, $currentContract->id());

        $commands = [];
        $commands[0] = [
            'switch_date' => DateTimeWrapper::create('2024-01-12 03:00:00 UTC'),
            'new_plan' => Plan::create(
                '100 Plan',
                100,
                100,
                MonetaryValue::create(100),
                true
            )
        ];
        $commands[0]['new_plan']->setId(100);

        $commands[1] = [
            'switch_date' => DateTimeWrapper::create('2024-04-01 03:00:00 UTC'),
            'new_plan' => Plan::create(
                '50 Plan',
                50,
                50,
                MonetaryValue::create(50),
                true
            )
        ];
        $commands[1]['new_plan']->setId(id: 50);

        return [
            'user' => $user,
            'current_contract_payments' => $currentContractPayments,
            'current_plan' => $currentPlan,
            'switch_contract_commands' => $commands
        ];
    }

    /**
     * @return Payment[]
     */
    private function switchContractSimpleTestPaymentsExpected(): array
    {
        $payments = [
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 0,
                "amount_charged" => 100,
                "credit_remaining" => 0,
                "due_date" => "2024-01-01T00:00:00.000000Z",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 200,
                "discount" => 51.61,
                "amount_charged" => 148.39,
                "credit_remaining" => 0,
                "due_date" => "2024-01-15T00:00:00.000000Z",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 400,
                "discount" => 26.67,
                "amount_charged" => 373.33,
                "credit_remaining" => 0,
                "due_date" => "2024-05-20T00:00:00.000000Z",
                "status" => "confirmed",
            ])
        ];

        return $payments;
    }

     /**
     * @return Payment[]
     */
    private function switchContractComplexTestPaymentsExpected(): array
    {
        $payments = [
            Payment::fromArray([
                "plan_price" => 800,
                "discount" => 0,
                "amount_charged" => 800,
                "credit_remaining" => 0,
                "due_date" => "2024-01-01 00:00:00 UTC",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 100,
                "amount_charged" => 0,
                "credit_remaining" => 390.32,
                "due_date" => "2024-01-12 03:00:00 UTC",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 100,
                "amount_charged" => 0,
                "credit_remaining" => 290.32,
                "due_date" => "2024-02-12 00:00:00 UTC",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 100,
                "amount_charged" => 0,
                "credit_remaining" => 190.32,
                "due_date" => "2024-03-12 00:00:00 UTC",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 100,
                "amount_charged" => 0,
                "credit_remaining" => 90.32,
                "due_date" => "2024-04-12 00:00:00 UTC",
                "status" => "canceled",
            ]),
            Payment::fromArray([
                "plan_price" => 100,
                "discount" => 90.32,
                "amount_charged" => 9.68,
                "credit_remaining" => 0,
                "due_date" => "2024-05-12 00:00:00 UTC",
                "status" => "canceled",
            ]),
            Payment::fromArray([
                "plan_price" => 50,
                "discount" => 50,
                "amount_charged" => 0,
                "credit_remaining" => 172.58,
                "due_date" => "2024-04-01 03:00:00 UTC",
                "status" => "confirmed",
            ]),
            Payment::fromArray([
                "plan_price" => 50,
                "discount" => 50,
                "amount_charged" => 0,
                "credit_remaining" => 122.58,
                "due_date" => "2024-05-01 00:00:00 UTC",
                "status" => "pending",
            ]),
            Payment::fromArray([
                "plan_price" => 50,
                "discount" => 50,
                "amount_charged" => 0,
                "credit_remaining" => 72.58,
                "due_date" => "2024-06-01 00:00:00 UTC",
                "status" => "pending",
            ]),
            Payment::fromArray([
                "plan_price" => 50,
                "discount" => 50,
                "amount_charged" => 0,
                "credit_remaining" => 22.58,
                "due_date" => "2024-07-01 00:00:00 UTC",
                "status" => "pending",
            ]),
            Payment::fromArray([
                "plan_price" => 50,
                "discount" => 22.58,
                "amount_charged" => 27.42,
                "credit_remaining" => 0,
                "due_date" => "2024-08-01 00:00:00 UTC",
                "status" => "pending",
            ])
        ];

        return $payments;
    }

    /**
     * @return Contract[]
     */
    private function switchContractSimpleTestContractsExpected(int $userId): array
    {
        $contracts = [
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 100,
                "active" => false,
            ]),
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 200,
                "active" => false,
            ]),
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 400,
                "active" => true,
            ])
        ];

        return $contracts;
    }

    /**
     * @return Contract[]
     */
    private function switchContractComplexTestContractsExpected(int $userId): array
    {
        $contracts = [
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 800,
                "active" => false,
            ]),
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 100,
                "active" => false,
            ]),
            Contract::fromArray([
                "user_id" => $userId,
                "plan_id" => 50,
                "active" => true,
            ]),
        ];

        return $contracts;
    }

    /**
     * @param Payment[] $expected
     * @param Payment[] $actual
     */
    private function assertPayments(array $expected, array $actual): void
    {
        foreach ($expected as $key => $_) {
            $a = $expected[$key]->toArray();
            $b = $actual[$key]->toArray();

            $this->assertEquals($a['plan_price'], $b['plan_price']);
            $this->assertEquals($a['discount'], $b['discount']);
            $this->assertEquals($a['amount_charged'], $b['amount_charged']);
            $this->assertEquals($a['credit_remaining'], $b['credit_remaining']);
            $this->assertEquals($a['due_date'], $b['due_date']);
            $this->assertEquals($a['status'], $b['status']);
        }
    }

    /**
     * @param Contract[] $expected
     * @param Contract[] $actual
     */
    private function assertContracts(array $expected, array $actual): void
    {
        foreach ($expected as $key => $_) {
            $a = $expected[$key]->toArray();
            $b = $actual[$key]->toArray();

            $this->assertEquals($a['user_id'], $b['user_id']);
            $this->assertEquals($a['plan_id'], $b['plan_id']);
            $this->assertEquals($a['active'], $b['active']);
        }
    }

}