<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
            ], 401);
        }

        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get(env('AUTH_SERVICE_URL') . '/me');

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized!',
                ], 401);
            }

            if ($response->status() !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized!',
                ], 401);
            }

            $user = $response->json();

            $request->merge(['user' => @$user['data']['user']]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token!',
            ], 401);
        }

        return $next($request);
    }
}
