<?php

namespace App\CQS;

use App\Domain\Entities\User;
use App\Repositories\ContractRepository;
use App\Repositories\PaymentRepository;
use \App\Domain\Entities\Contract;
use \App\Domain\Entities\Payment;
use \App\Domain\Entities\Plan;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;

class Queries
{
    private UserRepository $userRepository;
    private PlanRepository $planRepository;
    private ContractRepository $contractRepository;
    private PaymentRepository $paymentRepository;

    public function __construct(
        UserRepository $userRepository,
        PlanRepository $planRepository,
        ContractRepository $contractRepository,
        PaymentRepository $paymentRepository)
    {
        $this->userRepository = $userRepository;
        $this->planRepository = $planRepository;
        $this->contractRepository = $contractRepository;
        $this->paymentRepository = $paymentRepository;
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
    public function allContracts(User $user): array {
        return $this->contractRepository->fetchAll($user->id());
    }

    /**
     * @return Payment[]
     */
    public function allPaymentsByContractId(int $contractId): array {
        return $this->paymentRepository->fetchByContractId($contractId);
    }
}