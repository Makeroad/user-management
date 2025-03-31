<?php

namespace App\Usecase\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProfileAction
{
    public function __invoke(Request $request)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;

            $data = $this->validateInput($request);
            $this->updateUserInfo($userId, $data);

            Log::info('プロフィール更新成功', ['user_id' => $userId]);

            return response()->json(['message' => 'プロフィールを更新しました。']);
        } catch (\Throwable $e) {
            Log::error('プロフィール更新エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'プロフィールの更新に失敗しました。'], 500);
        }
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'postal_code' => 'nullable|string',
            'address' => 'required|string',
        ]);
    }

    //更新
    private function updateUserInfo($userId, $data)
    {
        $sql = 
        "
            UPDATE 
                users_info
            SET 
                name = :name
                ,phone = :phone
                ,postal_code = :postal_code
                ,address = :address
                ,updated_at = :updated_at
            WHERE 
                user_id = :user_id
        ";

        DB::update($sql, [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'updated_at' => now(),
            'user_id' => $userId,
        ]);
    }
}
