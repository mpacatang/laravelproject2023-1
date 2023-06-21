<?php

use App\Http\Controllers\Api\v1\InstallationController;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'v1/installations'], function () {
    Route::get('step_1', [InstallationController::class, 'get_step_1']);

    Route::get('step_2', [InstallationController::class, 'get_step_2']);
    Route::post('step_2', [InstallationController::class, 'update_step_2']);

    Route::get('step_3', [InstallationController::class, 'get_step_3']);
    Route::post('step_3/migrate', [InstallationController::class, 'step_3_migrate']);

    Route::get('step_4', [InstallationController::class, 'get_step_4']);
    Route::post('step_4', [InstallationController::class, 'step_4_create']);

    Route::get('step_5', [InstallationController::class, 'get_step_5']);
    Route::post('step_5', [InstallationController::class, 'update_step_5']);

});

