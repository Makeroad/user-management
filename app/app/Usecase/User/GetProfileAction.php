<?php

namespace App\Usecase\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetProfileAction
{
    public function __invoke(Request $request)
    {
        try {
            //ユーザーパラメータ取得
            $user = $request->get('user');

            $profile = $this->getProfile($user->id);

            if (!$profile) {
                Log::info('プロフィール取得失敗', ['user_id' => $user->id]);
                return response()->json(['error' => 'ユーザー情報が見つかりません。'], 404);
            }

            Log::info('プロフィール取得成功', ['user_id' => $user->id]);
            return response()->json($profile);
        } catch (\Throwable $e) {
            Log::error('プロフィール取得エラー', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'サーバーエラーが発生しました。'], 500);
        }
    }

    //ユーザーデータ取得
    private function getProfile($userId)
    {
        return DB::selectOne(
            "
                SELECT 
                    u.id,
                    u.email,
                    u.email_verified,
                    u.role,
                    u.del_flg,
                    u.created_at,
                    i.name,
                    i.phone,
                    i.postal_code,
                    i.address
                FROM 
                    users u
                INNER JOIN users_info i ON u.id = i.user_id
                WHERE 
                    u.id = :id
        ", ['id' => $userId]);
    }
}
