<?php

namespace App\Services;

use App\Data\QuestionData;
use App\Exceptions\CorrectAnswerIsNotSet;
use App\Models\Question;
use App\Models\Session as SessionModel;

class QuestionService
{
    /**
     * @param QuestionData $questionData
     * @return Question
     * @throws CorrectAnswerIsNotSet
     */
    public function createQuestion(QuestionData $questionData): Question
    {
        return Question::create($questionData->toArray());
    }

    public function updateQuestion(Question $question, QuestionData $questionData): bool
    {
        return $question->update($questionData->toArray());
    }

    public function findById(int $id): Question
    {
        return Question::find($id);
    }

}
