<?php

namespace App\Usecase\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordAction
{
    public function __invoke(Request $request)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;

            $data = $this->validateInput($request);

            $current = $this->getCurrentPassword($userId);

            if (!$current || !Hash::check($data['current_password'], $current->password)) {
                Log::info("パスワード変更失敗", ['user_id' => $userId]);
                return response()->json(['error' => '現在のパスワードが正しくありません。'], 400);
            }

            $this->updatePassword($userId, $data['new_password']);

            Log::info("パスワード変更成功", ['user_id' => $userId]);

            return response()->json(['message' => 'パスワードを変更しました。']);
        } catch (\Throwable $e) {
            Log::error("パスワード変更エラー", [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'エラーが発生しました。'], 500);
        }
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|min:6',
        ]);
    }

    //既存のパスワードと一致しているかどうか
    private function getCurrentPassword($userId)
    {
        return DB::selectOne(
        "
            SELECT 
                password 
            FROM 
                users 
            WHERE 
                id = :id
        ", ['id' => $userId]);
    }

    //パスワード更新
    private function updatePassword($userId, $newPassword)
    {
        DB::update(
            "
            UPDATE 
                users 
            SET 
                password = :password
                ,updated_at = :now 
            WHERE 
                id = :id
        ", [
            'password' => Hash::make($newPassword),
            'now' => now(),
            'id' => $userId
        ]);
    }
}
