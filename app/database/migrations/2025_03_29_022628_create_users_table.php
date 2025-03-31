<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('email_verified')->default(false);
            $table->tinyInteger('role')->default(1); // 1: 観光客, 2: ガイド, 3: 管理者
            $table->boolean('del_flg')->default(false);
            $table->timestamps();
        });

        // ダミーデータ作成
        DB::table('users')->insert([
            [
                'email' => 'guest1@example.com',
                'password' => Hash::make('password123'),
                'email_verified' => true,
                'role' => 1,
                'del_flg' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'guest2@example.com',
                'password' => Hash::make('password123'),
                'email_verified' => false,
                'role' => 1,
                'del_flg' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'guide1@example.com',
                'password' => Hash::make('password123'),
                'email_verified' => true,
                'role' => 2,
                'del_flg' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('adminpass'),
                'email_verified' => true,
                'role' => 3,
                'del_flg' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'deleted@example.com',
                'password' => Hash::make('password123'),
                'email_verified' => true,
                'role' => 1,
                'del_flg' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
