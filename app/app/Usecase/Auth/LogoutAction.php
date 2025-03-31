<?php

namespace App\Usecase\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogoutAction
{
    public function __invoke(Request $request)
    {
        try {
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                Log::warning('ログアウト失敗: トークンが無効または存在しません');
                return response()->json(['error' => 'トークンが見つかりません。'], 401);
            }

            $token = substr($authHeader, 7);

            DB::beginTransaction();

            $deleted = DB::delete("DELETE FROM tokens WHERE token = :token", ['token' => $token]);

            DB::commit();

            if ($deleted) {
                Log::info('ログアウト成功', ['token' => $token]);
                return response()->json(['message' => 'ログアウトしました。']);
            } else {
                Log::warning('ログアウト失敗: トークンがデータベースに存在しません', ['token' => $token]);
                return response()->json(['error' => 'ログアウトに失敗しました。'], 400);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ログアウトエラー', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'エラーが発生しました。'], 500);
        }
    }
}
