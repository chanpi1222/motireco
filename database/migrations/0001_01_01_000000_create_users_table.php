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
        // ユーザー情報を管理するメインテーブル
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); // ログインIDとして一意制約
            $table->timestamp('email_verified_at')->nullable(); // メール認証日時（未認証はnull）
            $table->string('password');
            $table->rememberToken(); // ログイン状態維持用トークン
            $table->timestamps();
        });

        // パスワードリセット用トークンを管理（メール単位で一意）
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // 1メールにつき1トークン
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // セッション情報をDBで管理（ログイン状態や操作履歴の保持）
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index(); // 未ログイン状態も考慮してnullable
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload'); // セッションデータ本体
            $table->integer('last_activity')->index(); // 最終アクセス時刻（Unixタイム）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
