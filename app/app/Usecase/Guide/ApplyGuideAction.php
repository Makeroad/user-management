<?php

namespace App\Usecase\Guide;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyGuideAction
{
    public function __invoke(Request $request)
    {
        try {
            $user = $request->get('user');
            $userId = $user->id;

            $data = $this->validateInput($request);

            DB::beginTransaction();

            $this->checkExistingRequest($userId);
            $requestId = $this->createGuideRequest($userId);
            $this->createGuideForm($requestId, $data);

            DB::commit();

            Log::info('ガイド申請成功', ['user_id' => $userId]);

            return response()->json(['message' => 'ガイド申請を受け付けました。']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ガイド申請失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => '申請に失敗しました。',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //バリデーション
    private function validateInput(Request $request)
    {
        return $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'languages' => 'required|string',
            'region' => 'required|string',
            'experience_years' => 'nullable|integer',
        ]);
    }

    //申請あるかどうか確認
    private function checkExistingRequest($userId)
    {
        $existing = DB::selectOne(
            "
                SELECT 
                    * 
                FROM 
                    guide_requests 
                WHERE 
                    user_id = :user_id
                ",
            ['user_id' => $userId]
        );

        if ($existing) {
            if ($existing->status === 1) {
                throw new \Exception('すでに申請中です。');
            }

            if ($existing->status === 2) {
                throw new \Exception('すでにガイドとして承認されています。');
            }

            // status === 3 の場合、申請を削除して再申請できるようにする
            DB::delete("DELETE FROM guide_application_forms WHERE request_id = :request_id", [
                'request_id' => $existing->id
            ]);
            DB::delete("DELETE FROM guide_requests WHERE user_id = :user_id", [
                'user_id' => $userId
            ]);
        }
    }

    //ガイド申請作成
    private function createGuideRequest($userId)
    {
        DB::insert(
            "INSERT INTO guide_requests (user_id, status, created_at, updated_at)
             VALUES (:user_id, 1, :now, :now)",
            [
                'user_id' => $userId,
                'now' => now()
            ]
        );

        return DB::getPdo()->lastInsertId();
    }

    //申請内容保存
    private function createGuideForm($requestId, $data)
    {
        DB::insert(
            "INSERT INTO guide_application_forms (request_id, title, description, languages, region, experience_years, created_at)
             VALUES (:request_id, :title, :description, :languages, :region, :experience_years, :now)",
            [
                'request_id' => $requestId,
                'title' => $data['title'],
                'description' => $data['description'],
                'languages' => $data['languages'],
                'region' => $data['region'],
                'experience_years' => $data['experience_years'],
                'now' => now()
            ]
        );
    }
}
