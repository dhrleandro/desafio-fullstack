<?php

namespace App\CQS;

use App\Domain\Entities\ContractPayments;
use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Services\ContractPaymentService;
use App\Domain\Services\SwitchContractService;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Exceptions\ResponseException;
use App\Repositories\ContractRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;

class Commands
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

    public function contractPlan(int $userId, int $planId, DateTimeWrapper $today): bool
    {
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        $plan = $this->planRepository->getById($planId);
        if (!$plan) {
            throw new ResponseException(
                'Plan not found',
                ['plan_id'=> $planId]
            );
        }

        $result = $this->contractPaymentService->createContract($user, $plan, $today);

        if (count($result->getPayments()) === 0) {
            return false;
        }

        $contractId = $this->contractRepository->create($result->getContract());
        foreach ($result->getPayments() as $payment) {
            $payment->setContractId($contractId);
            $this->paymentRepository->create($payment);
        }

        return true;
    }

    public function switchPlan(int $userId, int $newPlanId, DateTimeWrapper $today): bool
    {
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        if (!$user->hasActiveContract()) {
            throw new ResponseException(
                'User has no active contract',
                ['user_id'=> $userId]
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

        // update old contract
        $this->contractRepository->update($result->currentContract->getContract());
        foreach ($result->currentContract->getPayments() as $payment) {
            $this->paymentRepository->update($payment);
        }

        // create new contract
        $contractId = $this->contractRepository->create($result->newContract->getContract());
        foreach ($result->newContract->getPayments() as $payment) {
            $payment->setContractId($contractId);
            $this->paymentRepository->create($payment);
        }

        return true;
    }
}
