<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Contracts\IQuizHandlerFactory;
use App\Exceptions\QuizHandlerFactoryConditionsAreNotMet;
use App\Exceptions\QuizSessionDataIsCorrupted;
use App\Models\Session as SessionModel;

class QuizHandlerFactory implements IQuizHandlerFactory
{

    /**
     * @throws QuizHandlerFactoryConditionsAreNotMet
     */
    public function createHandler(SessionModel $session): ?IQuizHandler
    {

        if (empty($session->getCurrentQuestion())) {
            return new SendNextPollHandler($session);
        }

        if ($this->isTimeToAnswerExpired($session)) {

            if (empty($session->getQuestionsToGo())) {
                return new FinishQuizHandler($session);
            }

            return new SendNextPollHandler($session);
        }

        throw new QuizHandlerFactoryConditionsAreNotMet();
    }

    protected function isTimeToAnswerExpired(SessionModel $session): bool
    {
        return $session->updated_at->addSeconds(config('telegramBot.time_to_answer'))->isPast();
    }

}
