<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('register',[AuthController::class, 'register']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('list', [UserController::class, 'index']);
        Route::post('store',[UserController::class,'store']);
        Route::get('show',[UserController::class,'show']);
        Route::patch('update',[UserController::class,'update']);
        Route::delete('delete',[UserController::class,'delete']);
    });
});
