<?php

use App\Http\Controllers\ProductController;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(ValidateApiToken::class)->group(function () {
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
});
