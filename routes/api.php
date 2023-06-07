<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    //Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    //Route::get('/restore', [AuthController::class, 'restore']);
    Route::group(['middleware' => 'auth:survey'], function () {
//        Route::post('/check', [AuthController::class, 'check']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});


Route::group(['middleware' => 'auth:survey'], function () {
    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [UserController::class, 'index']);
//        Route::get('/select-list', [UserController::class, 'userSelectList']);
//        Route::get('/profile', [UserController::class, 'profile']);
//        Route::get('/{id}/transaction-history', [UserController::class, 'transactionHistory']);
//        Route::get('/{id}', [UserController::class, 'show']);
//        Route::put('/{id}', [UserController::class, 'update'])->middleware('verifyUserPassword');
//        Route::post('/', [UserController::class, 'store'])->middleware('verifyUserPassword');
//        Route::delete('/{id}', [UserController::class, 'delete'])->middleware('verifyUserPassword');
    });
});
