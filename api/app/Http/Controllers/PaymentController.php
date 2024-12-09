<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;
use App\UseCases\PaymentUseCases;

class PaymentController extends Controller
{
    protected PaymentUseCases $paymentUseCases;

    public function __construct(PaymentUseCases $paymentUseCases)
    {
        $this->paymentUseCases = $paymentUseCases;
    }

    public function index(): JsonResponse
    {
        $payments = $this->paymentUseCases->getAllPaymentsByContractId(1);
        return response()->json($payments);
    }
}
