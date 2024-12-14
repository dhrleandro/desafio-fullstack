<?php

namespace App\Http\Controllers;

use App\CQS\Queries;
use App\Domain\Entities\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected Queries $queries;

    public function __construct(Queries $queries)
    {
        $this->queries = $queries;
    }

    public function index(): JsonResponse
    {
        $userId = config("api.user_id");
        $payments = $this->queries->allPayments($userId);

        $paymentsArray = array_map(fn (Payment $payment) => $payment->toArray(), $payments);

        return response()->json($paymentsArray);
    }
}
