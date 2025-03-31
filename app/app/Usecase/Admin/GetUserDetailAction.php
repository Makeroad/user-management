<?php

namespace App\Usecase\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetUserDetailAction
{
    public function __invoke($id)
    {
        try {
            $user = $this->getUser($id);

            if (!$user) {
                Log::info('ユーザーが見つかりません', ['user_id' => $id]);
                return response()->json(['error' => 'ユーザーが見つかりません。'], 404);
            }

            return response()->json(['user' => $user]);
        } catch (\Throwable $e) {
            Log::error('ユーザー詳細取得失敗', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'ユーザー情報の取得に失敗しました。'], 500);
        }
    }

    //ユーザー情報取得
    private function getUser($id)
    {
        $sql = "
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
            LEFT JOIN users_info i ON u.id = i.user_id
            WHERE 
                u.id = :id
                AND u.del_flg = false
            ORDER BY 
                u.created_at DESC
        ";

        return DB::selectOne($sql, ['id' => $id]);
    }
}
