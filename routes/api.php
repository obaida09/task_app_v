<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\logoutController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GiftCardController;

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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [logoutController::class, 'logout']);

Route::middleware(['CheckAuth'])->group(function ()
{
    Route::resource('user', UserController::class);
    Route::resource('giftCard', GiftCardController::class);
    
    Route::get('/profile', [UserController::class, 'userProfile']);
    Route::post('useGiftCard', [GiftCardController::class, 'useGiftCard'])->middleware('dailyCardLimit');
});

