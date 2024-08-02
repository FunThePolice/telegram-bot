<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Data\OptionData;
use App\Data\Requests\PollData;
use App\Data\Responses\PollUpdateData;
use App\Models\Session as SessionModel;
use App\Services\TelegramBotService;

class SendNextPollHandler implements IQuizHandler
{

    protected SessionModel $session;

    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    public function handle(TelegramBotService $botService): void
    {
        $sessionQuestions = collect(json_decode($this->session->questions_to_go));
        $question = $sessionQuestions->shuffle()->first();
        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll(PollData::from([
            'chatId' => $this->session->chat_id,
            'text' => $question->body,
            'options' => OptionData::collect(json_decode($question->answers, true)),
            'openPeriod' => (int) config('telegramBot.time_to_answer'),
        ]));

        $this->session->update([
            'questions_to_go' => json_encode($updatedQuestionsToGo),
            'current_question' => json_encode([
                'question' => $question->body,
                'correctId' => $poll->getCorrectOptionId(),
            ]),
            'poll_id' => $poll->getPollId()
        ]);
    }

}
