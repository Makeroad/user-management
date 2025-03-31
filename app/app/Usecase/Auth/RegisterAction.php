<?php

namespace App\Usecase\Auth;

use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterAction
{
    public function __invoke(Request $request)
    {
        $validated = $this->validateInput($request);

        DB::beginTransaction();

        try {
            $userId = $this->createUser($validated);
            $this->createUserInfo($userId, $validated);
            $this->saveVerificationCode($validated['email']);

            DB::commit();

            Log::info('ユーザー登録成功', ['user_id' => $userId]);

            return response()->json([
                'message' => '仮登録が完了しました。メールをご確認ください。',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ユーザー登録失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => '登録に失敗しました。',
                'message' => $e->getMessage()
            ], 500);
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
    }

    //ユーザー作成
    private function createUser($data)
    {
        DB::insert(
            "INSERT INTO users (email, password, email_verified, role, del_flg, created_at, updated_at)
            VALUES (:email, :password, :email_verified, :role, :del_flg, :created_at, :updated_at)",
            [
                'email' => $data['email'],
                'password' => Hash::make($data['password']),//Hash化
                'email_verified' => false,
                'role' => 1,//一般ユーザー
                'del_flg' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return DB::getPdo()->lastInsertId();
    }

    //個人情報作成
    private function createUserInfo($userId, $data)
    {
        DB::insert(
            "INSERT INTO users_info (user_id, name, phone, postal_code, address, created_at, updated_at)
            VALUES (:user_id, :name, :phone, :postal_code, :address, :created_at, :updated_at)",
            [
                'user_id' => $userId,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'postal_code' => $data['postal_code'],
                'address' => $data['address'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    //メール認証コード作成
    private function saveVerificationCode(string $email)
    {
        //6桁ランダム数字
        $code = rand(100000, 999999);

        DB::insert(
            "INSERT INTO email_verifications (email, code, verified, expires_at, created_at, updated_at)
            VALUES (:email, :code, :verified, :expires_at, :created_at, :updated_at)
            ON CONFLICT(email) DO UPDATE SET
                code = excluded.code,
                verified = excluded.verified,
                expires_at = excluded.expires_at,
                updated_at = excluded.updated_at",
            [
                'email' => $email,
                'code' => $code,
                'verified' => false,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Log::info('認証コード発行', ['email' => $email, 'code' => $code]);

        //メール認証コード送信
        Mail::to($email)->send(new VerificationCodeMail($code));
    }
}