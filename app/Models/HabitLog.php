<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    // Factoryを利用してテストデータの生成を可能にする
    use HasFactory;

    // 一括代入を許可するカラム
    // habit_id（どの習慣か）と date（いつ達成したか）のみを管理するシンプルな設計
    protected $fillable = ['habit_id', 'date'];

    /**
     * dateカラムをCarbonインスタンスとして扱う設定
     *
     * これにより：
     * ・日付比較（whereDate / diffなど）が容易になる
     * ・フォーマット変換が柔軟にできる
     * ・「今日のログ判定」などのロジックが書きやすくなる
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Habitとのリレーション（多対1）
     *
     * 1つのログは1つの習慣に属する
     *
     * この定義により：
     * $log->habit で紐づく習慣を取得可能
     * Habit側の hasMany と対になる関係
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
