<?php

namespace App\Http\Controllers;

use App\Usecase\Admin\DeleteUserAction;
use Illuminate\Http\Request;
use App\Usecase\Admin\GetUserListAction;
use App\Usecase\Admin\GetUserDetailAction;
use App\Usecase\Admin\UpdateUserAction;

class AdminUserController extends Controller
{
    //ユーザー情報取得
    public function userlist(GetUserListAction $action)
    {
        return $action();
    }

    //ユーザー情報詳細取得
    public function userdetail($id, GetUserDetailAction $action)
    {
        return $action($id);
    }

    //ユーザー更新
    public function updateuser($id, Request $request, UpdateUserAction $action)
    {
        return $action($id,$request);
    }

    //ユーザーデータ削除(完全削除じゃない)
    public function deleteuser($id, DeleteUserAction $action)
    {
        return $action($id);
    }

}
