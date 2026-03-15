<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'todo' => '未着手',
            'doing' => '進行中',
            'done' => '完了',
            default => '不明',
        };
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\HabitLog::class);
    }
}
