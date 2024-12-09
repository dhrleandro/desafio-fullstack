<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;
use App\UseCases\PaymentsUseCases;

class PaymentController extends Controller
{
    protected PaymentsUseCases $paymentUseCases;

    public function __construct(PaymentsUseCases $paymentUseCases)
    {
        $this->paymentUseCases = $paymentUseCases;
    }

    public function index(): JsonResponse
    {
        $payments = $this->paymentUseCases->getAllPaymentsByContractId(1);
        return response()->json($payments);
    }
}
