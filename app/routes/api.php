<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\AdminGuideController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//初期画面
Route::post('/register', [AuthController::class, 'register']);
Route::post('/email/verify', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth.token')->group(function () {//認証
    Route::get('/auth', [AuthController::class, 'auth']);
    Route::get('/logout', [AuthController::class, 'logout']);

    //普通のユーザーのマイページ
    Route::prefix('/mypage')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile/change', [UserController::class, 'updateProfile']);
        // Route::post('/email/change', [UserController::class, 'requestEmailChange']);
        // Route::post('/email/verify', [UserController::class, 'verifyEmailChange']);
        Route::post('/password/change', [UserController::class, 'changePassword']);

        //ガイド申請周り
        Route::prefix('/guide')->group(function () {
            Route::post('/apply', [GuideController::class, 'apply']);
            Route::get('/status', [GuideController::class, 'getStatus']);
        });
    });

    //管理者専用
    Route::middleware('admin.only')->group(function () {
        Route::prefix('/admin')->group(function () {

            // ユーザー管理
            Route::get('/users', [AdminUserController::class, 'userlist']);
            Route::get('/users/{id}', [AdminUserController::class, 'userdetail']);
            Route::put('/users/{id}', [AdminUserController::class, 'updateuser']);
            Route::delete('/users/{id}', [AdminUserController::class, 'deleteuser']);

            // ガイド申請管理
            Route::prefix('/guide-requests')->group(function () {
                Route::get('/', [AdminGuideController::class, 'list']);
                Route::post('/{id}/approve', [AdminGuideController::class, 'approve']);
                Route::post('/{id}/reject', [AdminGuideController::class, 'reject']);
            });
        });
    });
});



