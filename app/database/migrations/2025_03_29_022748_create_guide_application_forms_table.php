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
        Schema::create('guide_application_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('guide_requests')->onDelete('cascade');
            $table->string('title'); // 申請タイトル
            $table->text('description'); // 自己紹介など
            $table->string('languages'); // 対応可能な言語（例：韓国語、日本語）
            $table->string('region'); // 活動地域（例：名古屋、岐阜、三重など）
            $table->tinyInteger('experience_years')->nullable(); // 経験年数（任意）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_application_forms');
    }
};
