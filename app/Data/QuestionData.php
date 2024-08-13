<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{

    public string $text;

    public array $answers;

    public array $correctAnswerIds;

    public function getText(): string
    {
        return $this->text;
    }

    public function getAnswers(): array
    {
        return $this->filterAnswers();
    }

    public function getOptions(): Collection
    {
        return Collection::make($this->getAnswers());
    }

    public function getCorrectAnswerIds(): Collection
    {
        return Collection::make($this->correctAnswerIds);
    }

    protected function filterAnswers(): array
    {
        return Collection::make($this->answers)->filter()->toArray();
    }

    public function toArray(): array
    {
        return [
            'body' => $this->getText(),
            'answers' => $this->getAnswers(),
            'correct_answer' => $this->getCorrectAnswerIds()
        ];
    }

}
