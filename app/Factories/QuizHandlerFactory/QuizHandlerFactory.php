<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Contracts\IQuizHandlerFactory;
use App\Exceptions\QuizHandlerFactoryConditionsAreNotMet;
use App\Exceptions\QuizSessionDataIsCorrupted;
use App\Models\Session as SessionModel;
use App\Services\Poll;

class QuizHandlerFactory implements IQuizHandlerFactory
{

    /**
     * @throws QuizHandlerFactoryConditionsAreNotMet
     */
    public function createHandler(Poll $poll): ?IQuizHandler
    {

        if ($poll->getCurrentQuestion()) {
            return new SendNextPollHandler($poll);
        }

        if ($poll->isTimeToAnswerExpired($poll)) {

            if ($poll->getQuestionsToGo()->isEmpty()) {
                return new FinishQuizHandler($poll);
            }

            return new SendNextPollHandler($poll);
        }

        throw new QuizHandlerFactoryConditionsAreNotMet();
    }

//    protected function isTimeToAnswerExpired(SessionModel $session): bool
//    {
//        return $session->updated_at->addSeconds(config('telegramBot.time_to_answer'))->isPast();
//    }

}
