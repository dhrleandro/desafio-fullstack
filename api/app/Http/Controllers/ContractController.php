<?php

namespace App\Http\Controllers;

use App\CQS\Commands;
use App\CQS\Queries;
use App\Domain\Entities\Contract;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Exceptions\ResponseException;
use DB;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use App\UseCases\ContractUseCases;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ContractController extends Controller
{
    protected Commands $commands;
    protected Queries $queries;

    public function __construct(Commands $commands, Queries $queries)
    {
        $this->commands = $commands;
        $this->queries = $queries;
    }

    public function index(): JsonResponse
    {
        $userId = config("api.user_id");

        $user = $this->queries->userById($userId);
        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        $contracts = $this->queries->allContracts($user->id());
        $contractsArray = array_map(fn (Contract $contract) => $contract->toArray(), $contracts);
        return response()->json($contractsArray);
    }

    public function store(Request $request): JsonResponse
    {
        Log::info("ContractControler->store started");
        try {

            DB::beginTransaction();

            $data = $request->validate([
                'simulated_datetime' => 'date_format:Y-m-d',
                'plan_id' => 'required',
            ]);

            $today = isset($data['simulated_datetime'])
                ? new DateTimeWrapper($data['simulated_datetime'])
                : DateTimeWrapper::create();
            $today->setTime(0,0,0,0);

            $userId = config("api.user_id");

            $result = $this->commands->contractPlan($userId, $data['plan_id'], $today);
            if (!$result) {
                throw new ResponseException('Could not create contract');
            }

            DB::commit();

            return response()->json([], Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ContractControler->store error: {$e->getMessage()}. Rollback...");
            throw $e;
        }
    }

    public function switchPlan(Request $request): JsonResponse
    {
        Log::info("ContractControler->switchPlan started");
        try {

            DB::beginTransaction();

            $data = $request->validate([
                'simulated_datetime' => 'date_format:Y-m-d',
                'plan_id' => 'required',
            ]);

            $today = isset($data['simulated_datetime'])
                ? new DateTimeWrapper($data['simulated_datetime'])
                : DateTimeWrapper::create();
            $today->setTime(0,0,0,0);

            $userId = config("api.user_id");

            $result = $this->commands->switchPlan($userId, $data['plan_id'], $today);
            if (!$result) {
                throw new ResponseException('Could not switch contract to new plan');
            }

            DB::commit();

            return response()->json([], Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ContractControler->switchPlan error: {$e->getMessage()}. Rollback...");
            throw $e;
        }
    }

    public function calculatePayment(Request $request): JsonResponse
    {
        Log::info("ContractControler->paymentRequest started");
        try {

            $data = $request->validate([
                'simulated_datetime' => 'date_format:Y-m-d',
                'plan_id' => 'required',
            ]);

            $today = isset($data['simulated_datetime'])
                ? new DateTimeWrapper($data['simulated_datetime'])
                : DateTimeWrapper::create();
            $today->setTime(0,0,0,0);

            $userId = config("api.user_id");
            $user = $this->queries->userById($userId);
            if (!$user) {
                throw new ResponseException(
                    'User not found',
                    ['user_id'=> $userId]
                );
            }

            $payment = $user->hasActiveContract()
                ? $this->queries->simulatePaymentRequestSwitchPlan($user, $data['plan_id'], $today)
                : $this->queries->simulatePaymentRequestContractPlan($user, $data['plan_id'], $today);
            
            if (!$payment) {
                throw new ResponseException('Could not simulate payment request');
            }

            return response()->json($payment->toArray(), Response::HTTP_OK);

        } catch (\Throwable $e) {
            Log::error("ContractControler->paymentRequest error: {$e->getMessage()}. Rollback not needed...");
            throw $e;
        }
    }
}
