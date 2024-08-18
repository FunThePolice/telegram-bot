<?php

namespace App\Data\Models;

use App\Models\Question;
use Spatie\LaravelData\Data;

class CurrentQuestionDto extends Data
{

    public ?Question $question;

    public int $questionId;

    public function getCorrectIds(): array
    {
        return array_map('intval', $this->question->correct_answer);
    }

    public function getQuestion()
    {
        $this->question = Question::find($this->questionId);
        return $this;
    }

}
