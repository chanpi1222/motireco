<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * キュー（非同期処理）で使用するジョブ関連テーブルを作成
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index(); // キュー名（処理の分類）
            $table->longText('payload'); // 実行されるジョブの内容（シリアライズされたデータ）
            $table->unsignedTinyInteger('attempts'); // 実行試行回数
            $table->unsignedInteger('reserved_at')->nullable(); // workerに取得された時間（処理中判定）
            $table->unsignedInteger('available_at'); // 実行可能になる時間
            $table->unsignedInteger('created_at'); // 作成時間（timestamp型ではなくintで管理）
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name'); // バッチ処理の識別名
            $table->integer('total_jobs'); // 総ジョブ数
            $table->integer('pending_jobs'); // 未処理ジョブ数
            $table->integer('failed_jobs'); // 失敗ジョブ数
            $table->longText('failed_job_ids'); // 失敗したジョブID一覧
            $table->mediumText('options')->nullable(); // バッチオプション
            $table->integer('cancelled_at')->nullable(); // キャンセルされた時間
            $table->integer('created_at');
            $table->integer('finished_at')->nullable(); // 完了時間
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // 失敗ジョブの一意識別子
            $table->text('connection'); // 接続情報（queue接続名）
            $table->text('queue'); // キュー名
            $table->longText('payload'); // 実行内容
            $table->longText('exception'); // エラー内容
            $table->timestamp('failed_at')->useCurrent(); // 失敗時刻
        });
    }

    /**
     * テーブル削除（ロールバック用）
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
