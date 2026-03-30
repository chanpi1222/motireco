<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * キャッシュデータおよびロック管理用テーブルの作成
     * （Laravelのキャッシュドライバで使用）
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            // キャッシュキー（ユニーク）
            $table->string('key')->primary();

            // シリアライズされたキャッシュデータ本体
            $table->mediumText('value');

            // 有効期限（UNIXタイムスタンプ）
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            // ロックキー（ユニーク）
            $table->string('key')->primary();

            // ロック保持者（プロセス識別など）
            $table->string('owner');

            // ロックの有効期限（UNIXタイムスタンプ）
            $table->integer('expiration');
        });
    }

    /**
     * テーブル削除（ロールバック用）
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
