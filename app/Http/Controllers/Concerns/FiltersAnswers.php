<?php

namespace App\Http\Controllers\Concerns;

trait FiltersAnswers
{

    public function getCorrectAnswer(array $answers)
    {
        $answer = [];
        foreach ($answers as $item) {
            if (is_array($item) && count($item) > 1) {
                $answer = $item;
            }
        }

        return $answer['text'];
    }

    public function filterAnswers(array $answers): string
    {
        return collect($answers)
            ->filter(function ($answer) {
                return $answer['text'] !== null;
            })->toJson();
    }

}
