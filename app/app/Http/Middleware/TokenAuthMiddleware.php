<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => '認証情報がありません。'], 401);
        }

        $token = substr($authHeader, 7);

        $user = DB::selectOne(
            "
            SELECT 
                u.*
            FROM 
                tokens t
            JOIN 
                users u on t.user_id = u.id
            WHERE 
                t.token = :token
                AND t.expired_at > :now
        ", [
            'token' => $token,
            'now' => now(),
        ]);

        if (!$user) {
            return response()->json(['error' => '無効なトークンです。'], 401);
        }

        $request->attributes->set('user', $user);

        return $next($request);
    }
}
