<?php

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceLineController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ProjetController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\ConfigurationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
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
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Client
Route::prefix('client')->group(function () {
    Route::get('/', [ClientController::class, 'data']);
    Route::get('/nb', [ClientController::class, 'nbdata']);
});

// Project
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

// Offer Routes
Route::prefix('offer')->group(function () {
    Route::get('/', [OfferController::class, 'data']);
    Route::get('/nb', [OfferController::class, 'nbdata']);
});

// Invoice
Route::prefix('invoice')->group(function () {
    Route::get('/', [InvoiceController::class, 'data']);
    Route::get('/nb', [InvoiceController::class, 'nbdata']);
    Route::get('/chart/{annee?}/{mois?}', [InvoiceController::class, 'invoicePaymentSummary']);
});

// Payment
Route::prefix('payment')->group(function () {
    Route::get('/', [PaymentController::class, 'data']);
    Route::get('/nb', [PaymentController::class, 'nbdata']);
    Route::get('/sum', [PaymentController::class, 'sumpayment']);
    Route::get('/chart', [PaymentController::class,'monthlyRevenueChart']);
    Route::post('/update/{id}', [PaymentController::class, 'updateAmount']);
    Route::get('/delete/{id}', [PaymentController::class, 'deletePayment']);
});

// Invoice Line
Route::prefix('invoice-line')->group(function () {
    Route::get('/', [InvoiceLineController::class, 'data']);
    Route::get('/nb', [InvoiceLineController::class, 'nbdata']);
});

// Status
Route::prefix('status')->group(function () {
    Route::get('/', [StatusController::class, 'data']);
});

Route::post('/configuration', [ConfigurationController::class, 'insert']);