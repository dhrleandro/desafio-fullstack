<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponseException;
use \Illuminate\Http\JsonResponse;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Plan::all());
    }
}
