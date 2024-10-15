<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ChatGPTController;

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

Route::post('/chat', [ChatGPTController::class, 'chat']);
Route::post('/test', [ChatGPTController::class, 'test']);
Route::post('/saveResponse', [ChatGPTController::class, 'saveResponse']);
Route::post('/getPost', [ChatGPTController::class, 'getPost']);
