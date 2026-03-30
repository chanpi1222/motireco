<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // Factoryはテストデータやシーディングで利用する
    // Notifiableは通知機能（メール通知など）を扱うためのLaravel標準トレイト
    use HasFactory, Notifiable;

    /**
     * 一括代入を許可する属性
     *
     * create() や update() で安全に代入できる項目を限定し、
     * 意図しないカラム更新を防ぐために明示している
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * JSON化や配列化したときに外部へ出さない属性
     *
     * password や remember_token は機密性が高いため、
     * レスポンスやデバッグ出力時にも露出しないように隠す
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 属性の型変換設定
     *
     * email_verified_at:
     *   DB上の日時文字列をCarbonとして扱い、日付操作をしやすくする
     *
     * password:
     *   代入時に自動でハッシュ化し、平文保存を防ぐ
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 現在のXPからユーザーのレベルを計算する
     *
     * 100ごとの単純な線形上昇ではなく、
     * レベルが上がるほど必要XPが増える成長曲線にしている
     *
     * 目的：
     * ・序盤はレベルが上がりやすく達成感を出す
     * ・後半は簡単に上がりすぎないようにしてゲーム性を持たせる
     *
     * 計算イメージ：
     * Lv2に必要 = 100
     * Lv3に必要 = 150
     * Lv4に必要 = 225
     * ...というように、前回必要XPに rate を掛けて増やす
     */
    public function calcLevel(int $xp): int
    {
        // 初期レベルアップに必要なXP
        $base = 100;

        // 次レベルに必要なXPの増加率
        $rate = 1.5;

        // 初期状態はLv1から開始
        $level = 1;

        // 現在レベルから次に上がるために必要なXP
        $need = $base;

        // 手持ちXPとして扱い、レベルアップごとに消費していく
        $remain = $xp;

        // 必要XPを満たしている限りレベルアップを繰り返す
        while ($remain >= $need) {
            // そのレベルアップに必要なXPを消費
            $remain -= $need;

            // レベルを1上げる
            $level++;

            // 次のレベルで必要なXPを増加させる
            $need = (int) ceil($need * $rate);
        }

        return $level;
    }

    /**
     * 指定レベルに到達するまでに必要な累計XPを返す
     *
     * 例：
     * Lv1 => 0
     * Lv2 => 100
     * Lv3 => 100 + 150 = 250
     * Lv4 => 100 + 150 + 225 = 475
     *
     * 用途：
     * ・「次のレベルまであと何XPか」を表示する
     * ・進捗バーの現在値 / 最大値の計算に使う
     * ・UI上でレベル到達条件を説明しやすくする
     */
    public function xpForLevel(int $level): int
    {
        // 初期レベルアップに必要なXP
        $base = 100;

        // レベルごとの必要XP増加率
        $rate = 1.5;

        // Lv1は初期状態なので必要累計XPは0
        if ($level <= 1) return 0;

        // 現在のレベルアップに必要なXP
        $need = $base;

        // 指定レベルまでの累計XP
        $total = 0;

        // Lv1→Lv2, Lv2→Lv3 ... と順番に必要XPを合算する
        for ($lv = 1; $lv < $level; $lv++) {
            $total += $need;
            $need = (int) ceil($need * $rate);
        }

        return $total;
    }
}
