<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protexted routes
Route::middleware(["auth:sanctum"])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/is_logged_in', function () {
        return true;
    });

    // Route::post('/payment', [PaymentController::class, 'paymentProcess']);
    Route::post('/donate', [PaymentController::class, 'storeDonation']);
    Route::get('/donations', [PaymentController::class, 'getDonations']);
    Route::get('/donor_number', [StatisticsController::class, 'getDonorNumber']);
    Route::get('/donor_type', [StatisticsController::class, 'getDonorType']);
    Route::get('/recurring_type', [StatisticsController::class, 'getRecurringType']);
    Route::get('/registration_count', [StatisticsController::class, 'getUserRegistrationCountsLast7Days']);
    Route::get('/login_count', [StatisticsController::class, 'getUserLoginData']);
    Route::post('/mail', [StatisticsController::class, 'mail']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
