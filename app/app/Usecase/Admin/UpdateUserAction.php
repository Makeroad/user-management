<?php

namespace App\Usecase\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserAction
{
    public function __invoke($id, Request $request)
    {
        try {
            $data = $this->validateInput($request);

            DB::beginTransaction();

            $this->updateUser($id, $data);
            $this->updateUserInfo($id, $data);

            DB::commit();

            Log::info('管理者によるユーザー情報更新', ['user_id' => $id]);

            return response()->json(['message' => 'ユーザー情報を更新しました。']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ユーザー情報更新エラー', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => '更新に失敗しました。'], 500);
        }
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'postal_code' => 'nullable|string',
            'address' => 'required|string',
        ]);
    }

    //更新
    private function updateUser($id, $data)
    {
        $sql = "
            UPDATE
                users
            SET 
                email = :email
                ,updated_at = :updated_at
            WHERE 
                id = :id
        ";

        DB::update($sql, [
            'email' => $data['email'],
            'updated_at' => now(),
            'id' => $id,
        ]);
    }

    //個人情報更新
    private function updateUserInfo($id, $data)
    {
        $sql = "
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
            'user_id' => $id,
        ]);
    }
}
