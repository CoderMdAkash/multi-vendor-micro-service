<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// auth service routes
Route::any('/{authEndPoint}', function ($authAuthEndPoint) {
    
    $authAuthEndPoints = ['login', 'register', 'logout', 'me'];

    $method = request()->method();

    if(!in_array($authAuthEndPoint, $authAuthEndPoints)) {
        abort(404);
    }

    $headers = [
        'Accept' => 'application/json',
    ];

    if(in_array($authAuthEndPoint, ['logout', 'me'])) {
        $headers['Authorization'] = request()->header('Authorization');
    }

    $options = [
        'query' => request()->query(),
    ];

    if(request()->isJson()) {
        $options['json'] = request()->json()->all();
    }else{
        $options['form_params'] = request()->all();
    }

    try {
        $response = Http::withHeaders($headers)
            ->send($method, env('AUTH_SERVICE_URL').'/'.$authAuthEndPoint, $options);

        return response()->json($response->json(), $response->status());

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'User Service Unavailable',
            'error' => $th->getMessage(),
        ], 500);
    }


})->whereIn('authEndPoint', ['login', 'register', 'logout', 'me']);

// product service routes
Route::any('/products/{any?}', function ($any = null) {
    $method = request()->method();

    $headers = [
        'Accept' => 'application/json',
        'Authorization' => request()->header('Authorization'),
    ];

    $options = [
        'query' => request()->query(),
    ];

    if(request()->isJson()) {
        $options['json'] = request()->json()->all();
    }else{
        $options['form_params'] = request()->all();
    }

    try {
        $response = Http::withHeaders($headers)
            ->send($method, env('PRODUCT_SERVICE_URL').'/products/'.$any, $options);

        return response()->json($response->json(), $response->status());

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Product Service Unavailable',
            'error' => $th->getMessage(),
        ], 500);
    }

});

// vendor service routes
Route::any('/vendors/{any?}', function ($any = null) {
    $method = request()->method();

    $headers = [
        'Accept' => 'application/json',
        'Authorization' => request()->header('Authorization'),
    ];

    $options = [
        'query' => request()->query(),
    ];

    if(request()->isJson()) {
        $options['json'] = request()->json()->all();
    }else{
        $options['form_params'] = request()->all();
    }

    try {
        $response = Http::withHeaders($headers)
            ->send($method, env('VENDOR_SERVICE_URL').'/vendors/'.$any, $options);

        return response()->json($response->json(), $response->status());

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Vendor Service Unavailable',
            'error' => $th->getMessage(),
        ], 500);
    }

});
