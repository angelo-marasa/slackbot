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
    Route::post('/velocity', [ApiController::class, 'getVelocityList']);
    Route::post('/am', [ApiController::class, 'getAccountManagers']);
    Route::post('/status', [ApiController::class, 'getStatus']);
    Route::post('/live-url', [ApiController::class, 'LiveURL']);
    Route::post('/staging-url', [ApiController::class, 'StagingURL']);
    Route::post('/launch', [ApiController::class, 'getLaunchDate']);
    Route::post('/clients', [ApiController::class, 'getClientList']);
    Route::post('/retainer', [ApiController::class, 'getRetainerDetails']);
    Route::post('/hosted', [ApiController::class, 'getHostingInformation']);
    Route::post('/dashboards', [ApiController::class, 'getDashboardsList']);
    Route::post('/dashboard', [ApiController::class, 'getDashboardDetails']);
});
