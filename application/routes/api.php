<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['cors', 'auth:api', 'json.response']], function () {
    Route::group([
        'as' => 'auth.',
    ], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login')->withoutMiddleware('auth:api');

        Route::post('/login/google', [AuthController::class, 'getAuthUrl'])->name('login_google')->withoutMiddleware('auth:api');
        Route::get('/login/google/callback', [AuthController::class, 'handleGoogleCallback'])->withoutMiddleware('auth:api');
    });

    Route::group([
        'as' => 'users.',
        'middleware' => ['json.response'],
    ], function () {
        Route::get('/users', [UsersController::class, 'index'])->name('index');
    });
});