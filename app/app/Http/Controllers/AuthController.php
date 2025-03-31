<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usecase\Auth\RegisterAction;
use App\Usecase\Auth\VerifyEmailAction;
use App\Usecase\Auth\LoginAction;
use App\Usecase\Auth\AuthAction;
use App\Usecase\Auth\LogoutAction;

class AuthController extends Controller
{
    //会員登録
    public function register(Request $request, RegisterAction $action)
    {
        return $action($request);
    }

    //メール認証
    public function verifyEmail(Request $request, VerifyEmailAction $action)
    {
        return $action($request);
    }

    //認証
    public function auth(Request $request, AuthAction $action)
    {
        return $action($request);
    }

    //ログイン
    public function login(Request $request, LoginAction $action)
    {
        return $action($request);
    }    

    //ログアウト
    public function logout(Request $request, LogoutAction $action)
    {
        return $action($request);
    }

}
