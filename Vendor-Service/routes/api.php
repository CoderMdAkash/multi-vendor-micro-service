<?php

use App\Http\Controllers\Api\VendorController;
use App\Http\Middleware\ValidateApiToken;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(ValidateApiToken::class)->group(function () {
    Route::resource('vendors', VendorController::class)->only(['index', 'store', 'update', 'destroy']);
});