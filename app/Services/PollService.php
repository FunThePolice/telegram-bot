<?php

namespace App\Services;

use App\Data\Requests\PollData;
use App\Data\Requests\QuizData;
use App\Models\Question;
use App\Models\Session as SessionModel;
use Illuminate\Support\Collection;

class PollService
{

    protected SessionModel $session;

    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    public function getPollData(Question $question): QuizData|PollData
    {
        return $this->hasMultipleCorrectAnswers($question) ?
            PollData::from([
                'chatId' => $this->session->getChatId(),
                'text' => $question->getBody(),
                'options' => $question->getAnswers(),
                'correctOptionIds' => $question->getCorrectAnswers(),
                'openPeriod' => (int) config('telegramBot.time_to_answer'),
            ]) :
            QuizData::from([
                'chatId' => $this->session->getChatId(),
                'text' => $question->getBody(),
                'options' => $question->getAnswers(),
                'correctOptionIds' => $question->getCorrectAnswers(),
                'openPeriod' => (int) config('telegramBot.time_to_answer'),
            ]);
    }

    protected function hasMultipleCorrectAnswers(Question $question): bool
    {
        return Collection::make($question->getCorrectAnswers())->count() > 1;
    }
}
