<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Contracts\IQuizHandlerFactory;
use App\Models\Session as SessionModel;

class QuizHandlerFactory implements IQuizHandlerFactory
{

    public function createHandler(SessionModel $session): ?IQuizHandler
    {

        if (empty($session->current_question)) {
            return new SendNextPollHandler($session);
        }

        if (empty(json_decode($session->questions_to_go))) {
            return new FinishQuizHandler($session);
        }

        if ($this->hasTimeToAnswerExpired($session)) {
            return new SendNextPollHandler($session);
        }

        return null;
    }

    protected function hasTimeToAnswerExpired(SessionModel $session): bool
    {
        return $session->updated_at->addSeconds(config('telegramBot.time_to_answer'))->isPast();
    }

}
