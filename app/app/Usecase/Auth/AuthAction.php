<?php

namespace App\Usecase\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthAction
{
    public function __invoke(Request $request)
    {
        try {
            // ユーザーIDがリクエストに存在しない場合はエラー
            if (!$request->user_id) {
                Log::warning('認証失敗: ユーザーIDが未設定');
                return response()->json(['error' => '不正なリクエストです。'], 400);
            }

            // DBからユーザー情報を取得
            $user = DB::selectOne(
                "
                SELECT 
                    u.id,
                    u.email,
                    i.name,
                    i.phone
                FROM 
                    users u
                INNER JOIN users_info i ON u.id = i.user_id
                WHERE 
                    u.id = :id
                ", [
                'id' => $request->user_id,
            ]);

            if (!$user) {
                Log::notice('認証失敗: 該当ユーザーが存在しません', ['user_id' => $request->user_id]);
                return response()->json(['error' => 'ユーザーが見つかりません。'], 404);
            }

            Log::info('ユーザー認証成功', ['user_id' => $user->id]);
            return response()->json(['user' => $user]);
        } catch (\Throwable $e) {
            Log::error('認証処理中にエラー発生', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'エラーが発生しました。'], 500);
        }
    }
}
