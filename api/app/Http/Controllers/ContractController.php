<?php

namespace App\Http\Controllers;

use App\CQS\Commands;
use App\CQS\Queries;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeWrapper;
use App\Exceptions\ResponseException;
use DB;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use App\UseCases\ContractUseCases;
use Log;

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

        $contracts = $this->queries->allContracts($user);
        return response()->json($contracts);
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
                throw new ResponseException('Could not create contract', 500);
            }

            DB::commit();

            return response()->json();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ContractControler->store error: {$e->getMessage()}. Rollback...");
            throw $e;
        }
    }
}
