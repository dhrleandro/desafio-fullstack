<?php

namespace App\Http\Controllers;

use App\CQS\Queries;
use App\Exceptions\ResponseException;
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
        return response()->json($payments);
    }
}
