<?php

namespace App\Services;

use App\Data\QuestionData;
use App\Models\Question;

class QuestionService
{
    /**
     * @param QuestionData $questionData
     * @return Question
     */
    public function createQuestion(QuestionData $questionData): Question
    {
        return Question::create($questionData->toArray());
    }

    public function updateQuestion(Question $question, QuestionData $questionData): bool
    {
        return $question->update($questionData->toArray());
    }

}
