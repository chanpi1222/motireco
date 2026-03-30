<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    // Factoryを使ってテストデータやシーディングを可能にする
    use HasFactory;

    // 一括代入（create / update）を許可するカラム
    // セキュリティ上、意図しないカラム更新を防ぐため明示的に指定
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /*
     * ステータスを日本語表示に変換するアクセサ
     *
     * DBには英語（todo / doing / done）で保存し、
     * 表示時のみ日本語に変換することで
     * ・DB設計の汎用性
     * ・View側のシンプル化
     * を両立している
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'todo' => '未着手',
            'doing' => '進行中',
            'done' => '完了',
            default => '不明', // 想定外の値に対する保険
        };
    }

    /**
     * Habit と HabitLog のリレーション（1対多）
     *
     * 1つの習慣に対して複数の日次ログが紐づく構造
     *
     * この定義により：
     * $habit->logs でログ一覧を取得可能
     * ->with('logs') によるEager LoadingでN+1問題を回避できる
     */
    public function logs()
    {
        return $this->hasMany(\App\Models\HabitLog::class);
    }
}
