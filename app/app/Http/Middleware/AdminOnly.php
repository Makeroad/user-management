<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->get('user'); 

        if (!$user || $user->role != 3) {
            return response()->json(['error' => '管理者権限が必要です。'], 403);
        }

        return $next($request);
    }
}
