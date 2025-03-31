<?php

namespace App\Usecase\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginAction
{
    public function __invoke(Request $request)
    {
        try {
            $validated = $this->validateInput($request);
            $user = $this->userCheck($validated['email']);

            if (!$user) {
                return response()->json(['error' => 'メールアドレスまたはパスワードが正しくありません。'], 401);
            }
            //削除されたユーザーか
            if ($user->del_flg) {
                return response()->json(['error' => 'このアカウントは無効です。'], 403);
            }
            //メール認証済か
            if (!$user->email_verified) {
                return response()->json(['error' => 'メール認証が完了していません。'], 403);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['error' => 'メールアドレスまたはパスワードが正しくありません。'], 401);
            }

            $token = $this->createToken($user->id);
            $this->logLogin($request, $user->id);

            Log::info("ログイン成功", ['user_id' => $user->id]);

            return response()->json([
                'message' => 'ログイン成功',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'postal_code' => $user->postal_code,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('ログイン処理失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'サーバーエラーが発生しました。'], 500);
        }
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    //ユーザーデータ確認
    private function userCheck($email)
    {
        $sql = 
        "
            SELECT 
                u.id,
                u.email,
                u.password,
                u.email_verified,
                u.del_flg,
                u.role,
                i.name,
                i.phone,
                i.address,
                i.postal_code
            FROM 
                users u
            INNER JOIN users_info i ON u.id = i.user_id
            WHERE 
                u.email = :email
            LIMIT 1
        ";

        $users = DB::select($sql, ['email' => $email]);
        return $users[0] ?? null;
    }

    //Token発行
    private function createToken($userId)
    {
        //Tokenがあれば一応削除
        DB::delete("DELETE FROM tokens WHERE user_id = :user_id", ['user_id' => $userId]);

        //token番号ランダムで抽出
        $token = Str::random(60);
        
        // DB保存
        DB::insert("INSERT INTO tokens (user_id, token, expired_at, created_at, updated_at)
            VALUES (:user_id, :token, :expired_at, :created_at, :updated_at)", [
            'user_id' => $userId,
            'token' => $token,
            'expired_at' => now()->addDay(),//１日有効
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $token;
    }

    //不具合調査のため環境ログを取得
    private function logLogin(Request $request, $userId)
    {
        $userAgent = $request->header('User-Agent') ?? 'unknown';
        $ipAddress = $request->ip();

        DB::insert("INSERT INTO login_logs (user_id, user_agent, ip_address, created_at)
            VALUES (:user_id, :user_agent, :ip_address, :created_at)", [
            'user_id' => $userId,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
