<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{

    public string $text;

    /** @var Collection<OptionData> */
    public Collection $answers;

    public string $correctAnswer;

    public function getText(): string
    {
        return $this->text;
    }

    public function getAnswers(): array
    {
        return $this->filterAnswers();
    }

    public function getCorrectAnswer(): string
    {
        $answer = [];
        foreach ($this->getAnswers() as $item) {
            if (isset($item['true'])) {
                $answer[] = $item;
            }

        }
        $correctAnswer = reset($answer);

        return $correctAnswer['text'] ?? '';
    }

    public function getCorrectAnswerId()
    {
        $options = Collection::make($this->answers);

        return $options->map(function ($option) {
            return $option->IsCorrect();
        })
            ->values()->search(function ($value) {
                return $value === true;
            });
    }

    protected function filterAnswers(): array
    {
        return $this->answers
            ->filter(function ($answer) {
                $answer = $answer->toArray();
                return $answer['text'] !== null;
            })->toArray();
    }

    public function toArray(): array
    {
        return [
            'body' => $this->text,
            'answers' => $this->getAnswers(),
            'correct_answer' => $this->getCorrectAnswerId()
        ];
    }

}
