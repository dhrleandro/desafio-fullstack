<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;
use App\UseCases\ContractUseCases;

class ContractController extends Controller
{
    protected ContractUseCases $contractUseCases;

    public function __construct(ContractUseCases $contractUseCases)
    {
        $this->contractUseCases = $contractUseCases;
    }

    public function index(): JsonResponse
    {
        $contracts = $this->contractUseCases->getAllContracts();
        return response()->json($contracts);
    }
}
