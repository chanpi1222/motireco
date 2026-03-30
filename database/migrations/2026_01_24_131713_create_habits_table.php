<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * habitsテーブル
     * ユーザーが管理する習慣の基本情報を保持
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            // 習慣名（例：筋トレ、読書など）

            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            // 習慣の進行状態
            // todo: 未着手 / doing: 進行中 / done: 完了
            // 現状はenumで簡易管理（将来的に別管理の余地あり）

            $table->timestamps();
            // 作成日時・更新日時
        });
    }

    /**
     * テーブル削除
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
