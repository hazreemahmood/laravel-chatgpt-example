<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\FileUploadController;



Route::get('/', function () {
    return view('home');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('file_upload');
    })->name('dashboard');

    Route::post('/file-upload', [FileUploadController::class, 'upload'])->name('file.upload');
    Route::get('/document-view/{fileName}', [FileUploadController::class, 'viewDocument'])->name('document.view');
    Route::post('/save-document/{fileName}', [FileUploadController::class, 'saveDocument'])->name('document.save');
});
