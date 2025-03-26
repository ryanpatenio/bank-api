<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $apiKey = $request->header('X-API-Key'); // Retrieve the API key from the request header

        if (!$apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Hash the API key
        $hashedApiKey = hash('sha256', $apiKey);
        
        // Fetch API key details in one query
        $key = ApiKey::where('api_key', $hashedApiKey)
            ->where('status', 'active')
            ->where('expires_at','>',Carbon::now())
            ->first();
        
        if (!$key) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key'
            ], 403);
        }

        return $next($request);
    }
}
