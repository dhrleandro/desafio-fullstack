<?php

namespace App\CQS;

use App\Domain\Entities\User;
use App\Exceptions\ResponseException;
use App\Repositories\ContractRepository;
use App\Repositories\PaymentRepository;
use \App\Domain\Entities\Contract;
use \App\Models\Payment as EloquentPayment;
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
    public function allContracts(int $userId): array {
        return $this->contractRepository->fetchAll($userId);
    }

    /**
     * @return EloquentPayment[]
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

        return $eloquentPayments->toArray();
    }
}