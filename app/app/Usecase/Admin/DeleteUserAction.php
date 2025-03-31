<?php

namespace App\Usecase\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteUserAction
{
    public function __invoke($id)
    {
        try {
            $user = $this->getUser($id);

            if (!$user) {
                return response()->json(['error' => 'ユーザーが見つかりません。'], 404);
            }

            $this->softDelete($id);

            Log::info('ユーザー削除成功', ['user_id' => $id]);

            return response()->json(['message' => 'ユーザーを削除しました。']);
        } catch (\Throwable $e) {
            Log::error('ユーザー削除エラー', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'ユーザー削除に失敗しました。'], 500);
        }
    }

    //ユーザー情報取得
    private function getUser($id)
    {
        return DB::selectOne(
                "
                    SELECT 
                        id 
                    FROM 
                        users 
                    WHERE 
                        id = :id
                ", ['id' => $id]);
    }

    //削除(完全に削除はしない)
    private function softDelete($id)
    {
        DB::update(
            "
            UPDATE 
                users
            SET 
                del_flg = true
                ,updated_at = :updated_at
            WHERE 
                id = :id
        ", [
            'updated_at' => now(),
            'id' => $id,
        ]);
    }
}
