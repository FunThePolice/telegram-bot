<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Contracts\IQuizHandlerFactory;
use App\Exceptions\QuizHandlerFactoryConditionsAreNotMet;
use App\Services\SessionService;

class QuizHandlerFactory implements IQuizHandlerFactory
{

    /**
     * @throws QuizHandlerFactoryConditionsAreNotMet
     */
    public function createHandler(SessionService $sessionService): ?IQuizHandler
    {

        if (!$sessionService->getCurrentQuestion()) {
            return new SendNextPollHandler($sessionService);
        }

        if ($sessionService->isTimeToAnswerExpired()) {

            if ($sessionService->getQuestionsToGo()->isEmpty()) {
                return new FinishQuizHandler($sessionService);
            }

            return new SendNextPollHandler($sessionService);
        }

        throw new QuizHandlerFactoryConditionsAreNotMet();
    }

}
