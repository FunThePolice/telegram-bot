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
        //TODO Request пропускает вариант без указания правильных ответов хотя имеет свойство required в кастом request
        //TODO Имеет ли здесь место эксепшн и нужен ли он тут
        if (empty($questionData->correctAnswerIds)) {
            throw new CorrectAnswerIsNotSet();
        }

        return Question::create($questionData->toArray());
    }

    public function updateQuestion(Question $question, QuestionData $questionData): bool
    {
        return $question->update($questionData->toArray());
    }

    public function getRandomQuestion(SessionModel $sessionModel): ?SessionModel
    {
        $sessionQuestions = $sessionModel->getQuestionsToGo();
        return $sessionQuestions->shuffle()->first();
    }

}
