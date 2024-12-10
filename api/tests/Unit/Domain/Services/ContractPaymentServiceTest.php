<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Contract;
use App\Domain\Entities\ContractPayments;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Services\ContractPaymentService;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Domain\ValueObjects\MonetaryValue;
use PHPUnit\Framework\TestCase;

class ContractPaymentServiceTest extends TestCase
{
    private function contractPaymentService(): ContractPaymentService
    {
        return new ContractPaymentService();
    }

    public function test_create_contract_plan_when_plan_has_no_id_throws_domain_exception()
    {
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(ContractPaymentService::EX_PLAN_NOT_FOUND);

        $this->contractPaymentService()->createContract(
            User::create(1, null),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_create_contract_plan_when_plan_inactive_throws_domain_exception()
    {
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            false
        );
        $plan->setId(1);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(ContractPaymentService::EX_PLAN_IS_NOT_ACTIVE);

        $this->contractPaymentService()->createContract(
            User::create(1, null),
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }

    public function test_create_contract_plan_when_user_has_active_contract_throws_domain_exception()
    {
        $user = User::create(30, 58);
        $plan = Plan::create(
            'Premium Plan',
            20,
            10,
            MonetaryValue::create(10),
            true
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(ContractPaymentService::EX_USER_HAS_ACTIVE_CONTRACT);

        $this->contractPaymentService()->createContract(
            $user,
            $plan,
            DateTimeWrapper::create('2021-01-01 00:00:00 UTC')
        );
    }
}
