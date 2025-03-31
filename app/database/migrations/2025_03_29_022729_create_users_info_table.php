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
        // スキーマの作成（SQLiteでは無視されますが構造のために記述）
        // DB::statement('CREATE SCHEMA IF NOT EXISTS confidential');

        Schema::create('users_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // 氏名
            $table->string('phone'); // 電話番号
            $table->string('postal_code')->nullable(); // 郵便番号
            $table->string('address'); // 住所
            $table->timestamps();
        });

        // ダミーデータ
        DB::table('users_info')->insert([
            [
                'user_id' => 1,
                'name' => 'test1',
                'phone' => '090-1111-1111',
                'postal_code' => '460-0001',
                'address' => '名古屋市中区',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'name' => 'test2',
                'phone' => '090-2222-2222',
                'postal_code' => '500-0001',
                'address' => '岐阜',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'name' => 'test3',
                'phone' => '090-3333-3333',
                'postal_code' => '514-0001',
                'address' => '三重県津市',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'name' => 'test4',
                'phone' => '090-4444-4444',
                'postal_code' => '460-0012',
                'address' => '名古屋市東区',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'name' => 'test5',
                'phone' => '090-5555-5555',
                'postal_code' => '510-0003',
                'address' => '三重県',
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
        Schema::dropIfExists('users_info');
    }
};
