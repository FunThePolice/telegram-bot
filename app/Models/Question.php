<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Question extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['answers' => 'array', 'correct_answer' => 'array'];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function getAnswersView(): string
    {
        return implode(', ', $this->getAnswers());
    }

    public function getCorrectAnswerView(): string
    {
        $answersView = Collection::make($this->correct_answer)->map(function ($answer) {
            $answers = $this->getAnswers();
            $textCorrectAnswers[] = $answers[$answer];
            return $textCorrectAnswers;
        });

        return $answersView->flatten()->implode(', ');
    }

    public function getAnswers(): array
    {
        return $this->answers ?? [];
    }

    public function getBody(): string
    {
        return $this->body ?? '';
    }

    public function getCorrectAnswers(): array
    {
        return $this->correct_answer ?? [];
    }

}
