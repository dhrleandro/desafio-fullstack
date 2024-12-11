<?php

namespace App\CQS;

use App\Domain\Entities\Plan;
use App\Domain\Entities\User;
use App\Domain\Services\ContractPaymentService;
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

    public function __construct(
        UserRepository $userRepository,
        PlanRepository $planRepository,
        ContractRepository $contractRepository,
        PaymentRepository $paymentRepository,
        ContractPaymentService $contractPaymentService)
    {
        $this->userRepository = $userRepository;
        $this->planRepository = $planRepository;
        $this->contractRepository = $contractRepository;
        $this->paymentRepository = $paymentRepository;
        $this->contractPaymentService = $contractPaymentService;
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
}