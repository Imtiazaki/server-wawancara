<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScoringController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::put('/data/submit/{id}', [ScoringController::class, 'submitScoring']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/all', [AuthController::class, 'getAllUser']);
    
    //Scoring
    Route::post('/data', [ScoringController::class, 'getData']);
    Route::get('/data/{id}', [ScoringController::class, 'getDataByIds']);
    Route::post('/form', [ScoringController::class, 'getFormByName']);
    Route::post('/data/create', [ScoringController::class, 'registerScore']);
    Route::post('/data/start', [ScoringController::class, 'startScore']);
    
    Route::put('/data/update/{id}', [ScoringController::class, 'updateData']);
    Route::delete('/data/delete/{id}', [ScoringController::class, 'updateData']);
});
