<?php

use App\Http\Controllers\Api\ProjetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

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

Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('users', ['uses' => 'UserController@index']);
    });
});

Route::post('/login', [AuthController::class, 'login']);

// Client
Route::prefix('client')->group(function () {
    Route::get('/', [ClientController::class, 'data']);
    Route::get('/nb', [ClientController::class, 'nbdata']);
});

// Project Routes
Route::prefix('project')->group(function () {
    Route::get('/', [ProjetController::class, 'data']);
    Route::get('/nb', [ProjetController::class, 'nbdata']);
    Route::get('/chart', [ProjetController::class, 'getProjectCountByStatus']);  
});

// Task
Route::prefix('task')->group(function () {
    Route::get('/', [TaskController::class, 'data']);
    Route::get('/nb', [TaskController::class, 'nbdata']);
});