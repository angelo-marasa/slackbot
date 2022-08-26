<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/velocity', [ApiController::class, 'getVelocityList']);
    Route::get('/am', [ApiController::class, 'getAccountManagers']);
    Route::get('/live-url', [ApiController::class, 'LiveURL']);
    Route::get('/staging-url', [ApiController::class, 'StagingURL']);
    Route::get('/status', [ApiController::class, 'getStatus']);
    Route::get('/launch', [ApiController::class, 'getLaunchDate']);
    Route::post('/vel', [ApiController::class, 'getVelocityListTest']);
});
