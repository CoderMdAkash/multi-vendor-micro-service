<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token Expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token Invalid'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization Token not found'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during authentication'
            ], 500);
        }

        return $next($request);
    }
}