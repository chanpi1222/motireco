<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * habitsテーブルに達成条件（description）カラムを追加
     */
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            // 各習慣の「達成条件」を任意入力で保持するためのカラム
            $table->text('description')->nullable()->after('name');
        });
    }

    /**
     * 追加したdescriptionカラムを削除（ロールバック用）
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            // upで追加したカラムを元に戻す
            $table->dropColumn('description');
        });
    }
};
