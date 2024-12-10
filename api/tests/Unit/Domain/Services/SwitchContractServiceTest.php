<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Entities\Contract;
use App\Domain\Entities\ContractPayments;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Services\Result\SwitchContractResult;
use App\Domain\Services\SwitchContractService;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use PHPUnit\Framework\TestCase;

class SwitchContractServiceTest extends TestCase
{
    use SwitchContractServiceTestTrait;

    public function test_switch_contract_when_contract_id_is_null_throws_domain_exception()
    {
        $contract = Contract::create(10, 8, true);

        $user = User::create(10, 100);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );
        $plan->setId(8);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(SwitchContractService::EX_CONTRACT_NOT_FOUND);

        $this->SwitchContractService()->switchContract(
            $user,
            ContractPayments::create($contract, []),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_switch_contract_when_current_contract_is_inactive_throws_domain_exception()
    {
        $currentContract = Contract::create(10, 8, false);
        $currentContract->setId(10);

        $contract = Contract::create(10, 8, false);
        $contract->setId(5);

        $user = User::create(10, 10);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );
        $plan->setId(8);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(SwitchContractService::EX_CONTRACT_IS_NOT_ACTIVE);

        $this->SwitchContractService()->switchContract(
            $user,
            ContractPayments::create($contract, []),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_switch_contract_when_user_not_has_active_contract_throws_domain_exception()
    {
        $userId = 30;
        $contractId = 5;

        $contract = Contract::create(50, 8, true);
        $contract->setId($contractId);

        $user = User::create($userId, null);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );
        $plan->setId(8);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(SwitchContractService::EX_USER_NOT_HAS_ACTIVE_CONTRACT);

        $this->SwitchContractService()->switchContract(
            $user,
            ContractPayments::create($contract, []),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_switch_contract_when_user_already_have_this_plan_throws_domain_exception()
    {
        $userId = 30;
        $contractId = 5;
        $planId = 8;

        $contract = Contract::create($userId, $planId, true);
        $contract->setId($contractId);

        $user = User::create($userId, $contractId);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );
        $plan->setId($planId);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(SwitchContractService::EX_USER_ALREADY_HAVE_THIS_PLAN);

        $this->SwitchContractService()->switchContract(
            $user,
            ContractPayments::create($contract, []),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_switch_contract_when_get_last_confirmed_payment_throws_domain_exception()
    {
        $userId = 30;
        $contractId = 5;
        $planId = 4;

        $contract = Contract::create($userId, 1, true);
        $contract->setId($contractId);

        $user = User::create($userId, $contractId);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );
        $plan->setId($planId);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(SwitchContractService::EX_GET_LAST_CONFIRMED_PAYMENT);
        $this->SwitchContractService()->switchContract(
            $user,
            ContractPayments::create($contract, []),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_switch_plan_simple()
    {
        $testCommands = $this->switchContractSimpleTestInputs();

        $user = $testCommands['user'];
        $currentContractPayments = $testCommands['current_contract_payments'];
        $currentPlan = $testCommands['current_plan'];
        $switchContractCommands = $testCommands['switch_contract_commands'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(ContractPayments::class, $currentContractPayments);
        $this->assertInstanceOf(Plan::class, $currentPlan);

        $results = [];
        foreach ($switchContractCommands as $key => $switchCommand) {
            $switchDate = $switchCommand['switch_date'];
            $newPlan = $switchCommand['new_plan'];

            $this->assertInstanceOf(DateTimeWrapper::class, $switchDate);
            $this->assertInstanceOf(Plan::class, $newPlan);

            $result = $this->SwitchContractService()->switchContract(
                $user,
                $currentContractPayments,
                $newPlan,
                $switchDate
            );

            $currentContractPayments = clone $result->newContract;
            $currentContractPayments->getContract()->setId($key + 100);
            $user = User::create($user->id(), $currentContractPayments->getContract()->id());

            $new_result = new SwitchContractResult();
            $new_result->currentContract = $result->currentContract;
            $new_result->newContract = $currentContractPayments;
            $results[] = $new_result;
        }

        $outputPayments = [];
        $outputContracts = [];
        foreach ($results as $key => $result) {
            if (count($results) - 1 === $key) {
                $outputPayments = array_merge(
                    $outputPayments,
                    $result->currentContract->getPayments(),
                    $result->newContract->getPayments()
                );

                $outputContracts = array_merge(
                    $outputContracts,
                    [
                        $result->currentContract->getContract(),
                        $result->newContract->getContract()
                    ]
                );
                continue;
            }

            $outputPayments = array_merge(
                $outputPayments,
                $result->currentContract->getPayments()
            );

            $outputContracts = array_merge(
                $outputContracts,
                [
                    $result->currentContract->getContract(),
                ]
            );
        }

        $this->assertPayments($this->switchContractSimpleTestPaymentsExpected(), $outputPayments);
        $this->assertContracts($this->switchContractSimpleTestContractsExpected($user->id()), $outputContracts);
    }

    public function test_switch_plan_complex()
    {
        $testCommands = $this->switchContractComplexTestInputs();

        $user = $testCommands['user'];
        $currentContractPayments = $testCommands['current_contract_payments'];
        $currentPlan = $testCommands['current_plan'];
        $switchContractCommands = $testCommands['switch_contract_commands'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(ContractPayments::class, $currentContractPayments);
        $this->assertInstanceOf(Plan::class, $currentPlan);

        $results = [];
        foreach ($switchContractCommands as $key => $switchCommand) {
            $switchDate = $switchCommand['switch_date'];
            $newPlan = $switchCommand['new_plan'];

            $this->assertInstanceOf(DateTimeWrapper::class, $switchDate);
            $this->assertInstanceOf(Plan::class, $newPlan);

            $result = $this->SwitchContractService()->switchContract(
                $user,
                $currentContractPayments,
                $newPlan,
                $switchDate
            );

            $currentContractPayments = clone $result->newContract;

            // simulate association of user with the new contract
            $currentContractPayments->getContract()->setId($key + 100);
            $user = User::create($user->id(), $currentContractPayments->getContract()->id());

            $new_result = new SwitchContractResult();
            $new_result->currentContract = $result->currentContract;
            $new_result->newContract = $currentContractPayments;
            $results[] = $new_result;
        }

        $outputPayments = [];
        $outputContracts = [];
        foreach ($results as $key => $result) {
            if (count($results) - 1 === $key) {
                $outputPayments = array_merge(
                    $outputPayments,
                    $result->currentContract->getPayments(),
                    $result->newContract->getPayments()
                );

                $outputContracts = array_merge(
                    $outputContracts,
                    [
                        $result->currentContract->getContract(),
                        $result->newContract->getContract()
                    ]
                );
                continue;
            }

            $outputPayments = array_merge(
                $outputPayments,
                $result->currentContract->getPayments()
            );

            $outputContracts = array_merge(
                $outputContracts,
                [
                    $result->currentContract->getContract(),
                ]
            );
        }

        $this->assertPayments($this->switchContractComplexTestPaymentsExpected(), $outputPayments);
        $this->assertContracts($this->switchContractComplexTestContractsExpected($user->id()), $outputContracts);
    }
}
