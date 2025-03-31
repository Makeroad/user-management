<?php

namespace App\Usecase\Admin\Guides;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RejectGuideRequestAction
{
    public function __invoke($id)
    {
        try {
            $request = $this->getRequest($id);

            if (!$request) {
                return response()->json(['error' => '申請が見つかりません。'], 404);
            }

            if ($request->status != 1) {
                return response()->json(['error' => 'この申請はすでに処理されています。'], 400);
            }

            $this->rjected($id);

            Log::info('ガイド申請拒否', ['request_id' => $id, 'user_id' => $request->user_id]);

            return response()->json(['message' => '申請を拒否しました。']);
        } catch (\Throwable $e) {
            Log::error('ガイド申請拒否エラー', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => '申請の拒否に失敗しました。'], 500);
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
                WHERE 
                    id = :id
                ", ['id' => $id]);
    }

    //拒否
    private function rjected($id)
    {
        DB::update(
            "
            UPDATE 
                guide_requests
            SET 
                status = 3
                ,updated_at = :now
            WHERE 
                id = :id
        ", [
            'now' => now(),
            'id' => $id,
        ]);
    }
}
