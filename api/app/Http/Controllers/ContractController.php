<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;
use App\UseCases\ContractsUseCases;

class ContractController extends Controller
{
    protected ContractsUseCases $contractUseCases;

    public function __construct(ContractsUseCases $contractUseCases)
    {
        $this->contractUseCases = $contractUseCases;
    }

    public function index(): JsonResponse
    {
        $contracts = $this->contractUseCases->getAllContracts();
        return response()->json($contracts);
    }
}
