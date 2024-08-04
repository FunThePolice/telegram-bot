<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['answers' => 'array'];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function filterAnswers(): array
    {
        return collect($this->answers)
            ->filter(function ($answer) {
                return $answer['text'] !== null;
            })->toArray();
    }
}
