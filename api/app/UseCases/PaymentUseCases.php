<?php

namespace App\UseCases;

use App\Models\Contract;
use App\Repositories\PaymentRepository;

class PaymentUseCases
{
    private PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @return Contract[]
     */
    public function getAllPaymentsByContractId(int $contractId): array {
        return $this->paymentRepository->fetchByContractId($contractId);
    }
}