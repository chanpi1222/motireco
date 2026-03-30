<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * usersテーブルにXP（経験値）カラムを追加
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 習慣達成に応じて増減する累積XP
            $table->integer('xp')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     * 追加したXPカラムを削除
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('xp');
        });
    }
};
