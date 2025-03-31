<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usecase\Admin\Guides\ListGuideRequestsAction;
use App\Usecase\Admin\Guides\ApproveGuideRequestAction;
use App\Usecase\Admin\Guides\RejectGuideRequestAction;

class AdminGuideController extends Controller
{
    //ガイド申請書取得
    public function list(ListGuideRequestsAction $action)
    {
        return $action();
    }

    //承認
    public function approve($id, ApproveGuideRequestAction $action)
    {
        return $action($id);
    }

    //拒否
    public function reject($id, RejectGuideRequestAction $action)
    {
        return $action($id);
    }
}
