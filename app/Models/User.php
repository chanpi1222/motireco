<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
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

    public function calcLevel(int $xp): int
    {
        // ここを「成長曲線レベル計算」の本命に置き換えてください
        // 例：100ごとではない非線形計算など
        $base = 100;
        $rate = 1.5;

        $level = 1;
        $need = $base;
        $remain = $xp;

        while ($remain >= $need) {
            $remain -= $need;
            $level++;
            $need = (int) ceil($need * $rate);
        }
        return $level;
    }

    /**
     * 指定Lvに到達するために必要な「累計XP」
     * 例：Lv1は0、Lv2は100、Lv3は100+150=250...
     */

    public function xpForLevel(int $level): int
    {
        $base = 100;
        $rate = 1.5;

        if ($level <= 1) return 0;

        $need = $base;
        $total = 0;

        for ($lv = 1; $lv < $level; $lv++) {
            $total += $need;
            $need = (int) ceil($need * $rate);
        }
        return $total;
    }
}
