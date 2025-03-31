<?php

namespace App\Usecase\Admin\Guides;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListGuideRequestsAction
{
    public function __invoke()
    {
        try {
            $requests = $this->getPendingRequests();

            Log::info('ガイド申請一覧取得', ['count' => count($requests)]);

            return response()->json([
                'requests' => $requests
            ]);
        } catch (\Throwable $e) {
            Log::error('ガイド申請一覧取得失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => '申請一覧の取得に失敗しました。'
            ], 500);
        }
    }

    //申請中の申請書確認
    private function getPendingRequests()
    {
        $sql = 
        "
            SELECT 
                r.id AS request_id
                ,r.user_id
                ,r.status
                ,r.created_at
                ,u.email
                ,i.name
                ,f.title
                ,f.description
                ,f.languages
                ,f.region
                ,f.experience_years
            FROM 
                guide_requests r
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN users_info i ON u.id = i.user_id
            INNER JOIN guide_application_forms f ON r.id = f.request_id
            WHERE 
                r.status = 1
            ORDER BY 
                r.created_at DESC
        ";

        return DB::select($sql);
    }
}
