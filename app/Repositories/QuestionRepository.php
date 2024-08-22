<?php

namespace App\Repositories;

use App\Data\QuestionData;
use App\Exceptions\CorrectAnswerIsNotSet;
use App\Models\Question;
use Illuminate\Support\Collection;

class QuestionRepository
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

    public function findMultipleById(array $ids): Collection
    {
        return Question::whereIn('id', $ids)->get();
    }

}
