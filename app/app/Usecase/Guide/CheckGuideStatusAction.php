<?php

namespace App\Usecase\Guide;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckGuideStatusAction
{
    public function __invoke(Request $request)
    {
        try {
            $userId = $request->get('user')->id;
            $record = $this->getLatestRequest($userId);

            $status = $this->determineStatus($record);
            Log::info('ガイド申請ステータス取得', ['user_id' => $userId, 'status' => $status]);

            return response()->json(['status' => $status]);
        } catch (\Throwable $e) {
            Log::error('ガイド申請ステータス取得エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'unknown'], 500);
        }
    }

    //申請状態確認
    private function getLatestRequest($userId)
    {
        return DB::selectOne(
            "
            SELECT 
                status 
            FROM 
                guide_requests
            WHERE 
                user_id = :user_id
            ORDER BY id DESC
            LIMIT 1
            ",
            ['user_id' => $userId]
        );
    }

    //状態
    private function determineStatus($record)
    {
        if (!$record) return 'none';

        switch ($record->status) {
            case 1: return 'pending';//申請中
            case 2: return 'approved';//承認
            case 3: return 'rejected';//拒否
            default: return 'unknown';
        }
    }
}
