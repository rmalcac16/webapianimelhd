<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
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

if (env('ENCRYPTED_DATA'))
Route::middleware('encrypted')->group(function () {
    Route::get("releases", [Controller::class, 'releases']);
    Route::get("anime/list", [Controller::class, 'animes']);
    Route::get("anime/simulcast", [Controller::class, 'simulcast']);
    Route::get("anime/search", [Controller::class, 'search']);
    Route::get("anime/latino", [Controller::class, 'latino']);
    Route::get("anime/castellano", [Controller::class, 'castellano']);
    Route::get("anime/trending", [Controller::class, 'trending']);
    Route::get("anime/more-view", [Controller::class, 'moreview']);
    Route::get("anime/{slug}", [Controller::class, 'anime']);
    Route::get("anime/{slug}/episodes/{number}", [Controller::class, 'episode']);
});
else {
    Route::get("releases", [Controller::class, 'releases']);
    Route::get("anime/list", [Controller::class, 'animes']);
    Route::get("anime/simulcast", [Controller::class, 'simulcast']);
    Route::get("anime/search", [Controller::class, 'search']);
    Route::get("anime/latino", [Controller::class, 'latino']);
    Route::get("anime/castellano", [Controller::class, 'castellano']);
    Route::get("anime/trending", [Controller::class, 'trending']);
    Route::get("anime/more-view", [Controller::class, 'moreview']);
    Route::get("anime/{slug}", [Controller::class, 'anime']);
    Route::get("anime/{slug}/episodes/{number}", [Controller::class, 'episode']);
}

Route::post('/login', [AuthController::class, 'login']);
Route::get('/token/{episode_id}', [Controller::class, 'token']);