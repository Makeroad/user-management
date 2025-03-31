<?php

namespace App\Http\Controllers;

use App\Usecase\Guide\ApplyGuideAction;
use App\Usecase\Guide\CheckGuideStatusAction;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    //ガイド申請
    public function apply(Request $request, ApplyGuideAction $action)
    {
        return $action($request);
    }

    //申請状態確認
    public function getStatus(Request $request, CheckGuideStatusAction $action)
    {
        return $action($request);
    }
}
