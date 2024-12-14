<?php

namespace App\CQS;

use App\Domain\Entities\Payment;
use App\Domain\Entities\User;
use App\Domain\Services\ContractPaymentService;
use App\Domain\Services\SwitchContractService;
use App\Exceptions\ResponseException;
use App\Repositories\ContractRepository;
use App\Repositories\PaymentRepository;
use \App\Domain\Entities\Contract;
use \App\Models\Payment as EloquentPayment;
use \App\Domain\Entities\Plan;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use App\Domain\Entities\ContractPayments;
use App\Domain\ValueObjects\DateTimeWrapper;

class Queries
{
    private UserRepository $userRepository;
    private PlanRepository $planRepository;
    private ContractRepository $contractRepository;
    private PaymentRepository $paymentRepository;

    private ContractPaymentService $contractPaymentService;
    private SwitchContractService $switchContractService;

    public function __construct(
        UserRepository $userRepository,
        PlanRepository $planRepository,
        ContractRepository $contractRepository,
        PaymentRepository $paymentRepository,
        ContractPaymentService $contractPaymentService,
        SwitchContractService $switchContractService)
    {
        $this->userRepository = $userRepository;
        $this->planRepository = $planRepository;
        $this->contractRepository = $contractRepository;
        $this->paymentRepository = $paymentRepository;
        $this->contractPaymentService = $contractPaymentService;
        $this->switchContractService = $switchContractService;
    }

    public function userById(int $userId): ?User
    {
        return $this->userRepository->getById($userId);
    }

    /**
     * @return Plan[]
     */
    public function allPlans(): array
    {
        return $this->planRepository->fetchAll();
    }

    public function planById(int $planId): ?Plan
    {
        return $this->planRepository->getById($planId);
    }

    /**
     * @return Contract[]
     */
    public function allContracts(int $userId): array {
        return $this->contractRepository->fetchAll($userId);
    }

    /**
     * @return Payment[]
     */
    public function allPayments(int $userId): array {
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        if (!$user->hasActiveContract()) {
            return [];
        }

        $eloquentPayments = EloquentPayment::whereHas('contract.user', function ($query) use ($userId) {
            $query->where('id', $userId)->orderBy('created_at', 'asc');
        })->get();

        if (count($eloquentPayments) === 0) {
            return [];
        }

        $payments = array_map(fn (array $eloquentPayment) => Payment::fromArray($eloquentPayment), $eloquentPayments->toArray());

        return $payments;
    }

    public function simulatePaymentRequestContractPlan(User $user, int $planId, DateTimeWrapper $today): ?Payment
    {
        $plan = $this->planRepository->getById($planId);
        if (!$plan) {
            throw new ResponseException(
                'Plan not found',
                ['plan_id'=> $planId]
            );
        }

        $result = $this->contractPaymentService->createContract($user, $plan, $today);

        if (count($result->getPayments()) === 0) {
            return null;
        }

        return $result->getPayments()[0];
    }

    public function simulatePaymentRequestSwitchPlan(User $user, int $newPlanId, DateTimeWrapper $today): ?Payment
    {
        if (!$user->hasActiveContract()) {
            throw new ResponseException(
                'User has no active contract',
                ['user_id'=> $user->id()]
            );
        }

        $contract = $this->contractRepository->getById($user->activeContractId());
        if (!$contract) {
            throw new ResponseException(
                'Contract not found',
                ['contract_id'=> $user->activeContractId()]
            );
        }

        $newPlan = $this->planRepository->getById($newPlanId);
        if (!$newPlanId) {
            throw new ResponseException(
                'Plan not found',
                ['plan_id'=> $newPlanId]
            );
        }

        $payments = $this->paymentRepository->fetchByContractId($contract->id());
        if (count($payments) === 0) {
            throw new ResponseException(
                'Contract has no payments',
                ['contract_id'=> $contract->id()]
            );
        }

        $contractPayments = ContractPayments::create($contract, $payments);

        $result = $this->switchContractService->switchContract(
            $user,
            $contractPayments,
            $newPlan,
            $today
        );

        if (count($result->newContract->getPayments()) === 0) {
            return null;
        }

        return $result->newContract->getPayments()[0];
    }
}