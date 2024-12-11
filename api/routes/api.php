<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function () {
    return response()->json(['message' => 'ok']);
});

Route::apiResource('plans', PlanController::class, ['only' => 'index']);

Route::apiSingleton('user', UserController::class, ['only' => 'show']);

Route::apiResource('contracts', ContractController::class, ['only' => 'index', 'store']);

Route::controller(ContractController::class)->group(function () {
    Route::get('/contracts', 'index');
    Route::post('/contracts', 'store');
    Route::post('/contracts/switch-plan', 'switchPlan');
});

Route::apiResource('payments', PaymentController::class, ['only' => 'index', 'store']);
