<?php

namespace App\Usecase\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class VerifyEmailAction
{
    public function __invoke(Request $request)
    {
        $validated = $this->validateInput($request);

        // メールと認証コード確認
        $sql =
        "
            SELECT 
            * 
            FROM 
                email_verifications
            WHERE 
                email = :email 
                AND code = :code 
                AND verified = false
        ";

        $verification = DB::select($sql, [
            'email' => $validated['email'],
            'code' => $validated['code'],
        ]);

        // 認証コードがない場合は失敗
        if (empty($verification)) {
            return response()->json([
                'error' => '無効なコードです。',
            ], 400);
        }

        // 認証有効期限確認
        if (strtotime($verification[0]->expires_at) < time()) {
            return response()->json([
                'error' => '認証コードが期限切れです。',
            ], 400);
        }

        // 認証処理
        $updateSql = 
        "
            UPDATE 
                email_verifications
            SET 
                verified = true
                ,updated_at = :updated_at
            WHERE 
                email = :email 
                AND code = :code
        ";

        DB::update($updateSql, [
            'updated_at' => now(),
            'email' => $validated['email'],
            'code' => $validated['code'],
        ]);

        // 認証済
        $updateUserSql = 
        "
            UPDATE 
                users
            SET 
                email_verified = true
                ,updated_at = :updated_at
            WHERE 
                email = :email
        ";

        DB::update($updateUserSql, [
            'updated_at' => now(),
            'email' => $validated['email'],
        ]);

        Log::info("ユーザーメール認証成功", ['email' => $validated['email']]);

        return response()->json([
            'message' => 'メール認証が完了しました。',
        ], 200);
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);
    }
}
