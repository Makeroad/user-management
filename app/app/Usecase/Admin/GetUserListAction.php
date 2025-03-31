<?php

namespace App\Usecase\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetUserListAction
{
    public function __invoke()
    {
        try {
            $users = $this->getUser();

            return response()->json(['users' => $users]);
        } catch (\Throwable $e) {
            Log::error('ユーザー一覧取得エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'ユーザー一覧の取得に失敗しました。'], 500);
        }
    }

    //ユーザー情報取得
    private function getUser()
    {
        $sql = "
            SELECT 
                u.id,
                u.email,
                i.name,
                i.phone
            FROM    
                users u
            INNER JOIN users_info i ON u.id = i.user_id
            WHERE 
                u.del_flg = false
        ";

        return DB::select($sql);
    }
}
