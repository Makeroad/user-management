<?php

namespace App\Http\Controllers;

use App\Usecase\User\GetProfileAction;
use App\Usecase\User\UpdateProfileAction;
use App\Usecase\User\RequestEmailChangeAction;
use App\Usecase\User\VerifyEmailChangeAction;
use App\Usecase\User\ChangePasswordAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //情報取得
    public function getProfile(Request $request, GetProfileAction $action)
    {
        return $action($request);
    }
    
    //情報更新
    public function updateProfile(Request $request, UpdateProfileAction $action)
    {
        return $action($request);
    }

    //パスワード更新
    public function changePassword(Request $request, ChangePasswordAction $action)
    {
        return $action($request);
    }
}
