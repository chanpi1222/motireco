<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 習慣ログテーブル
     * 1つの習慣に対して「1日1回の達成記録」を保持する
     */
    public function up(): void
    {
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();

            // 習慣に紐づくログ（習慣削除時はログも削除）
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();

            // 達成日（toggle処理で当日の記録を管理）
            $table->date('date');

            $table->timestamps();

            // 同一習慣×同一日付での重複登録を防止（1日1記録ルール）
            $table->unique(['habit_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
    }
};
