<?php

namespace App\Usecase\Admin\Guides;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveGuideRequestAction
{
    public function __invoke($id)
    {
        DB::beginTransaction();

        try {
            $request = $this->getRequest($id);

            if (!$request) {
                return response()->json(['error' => '申請が見つかりません。'], 404);
            }

            if ($request->status != 1) {
                return response()->json(['error' => 'この申請はすでに処理されています。'], 400);
            }

            $this->userToGuide($request->user_id);
            $this->approved($id);

            DB::commit();

            Log::info('ガイド申請承認', ['request_id' => $id, 'user_id' => $request->user_id]);

            return response()->json(['message' => '申請を承認しました。']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ガイド申請承認エラー', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => '承認に失敗しました。'], 500);
        }
    }

    //申請書取得
    private function getRequest($id)
    {
        return DB::selectOne(
            "
                SELECT 
                    * 
                FROM 
                    guide_requests 
                WHERE id = :id
        ", ['id' => $id]);
    }

    //ガイド昇格
    private function userToGuide($userId)
    {
        DB::update(
            "
            UPDATE 
                users
            SET 
                role = 2
                ,updated_at = :now
            WHERE 
                id = :user_id
        ", [
            'now' => now(),
            'user_id' => $userId
        ]);
    }

    //申請書許可
    private function approved($requestId)
    {
        DB::update(
            "
            UPDATE 
                guide_requests
            SET 
                status = 2
                , updated_at = :now
            WHERE 
                id = :id
        ", [
            'now' => now(),
            'id' => $requestId
        ]);
    }
}
