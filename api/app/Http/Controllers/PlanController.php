<?php

namespace App\Http\Controllers;

use \Illuminate\Http\JsonResponse;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Plan::all());
    }
}
