<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Data\OptionData;
use App\Data\QuestionData;
use App\Data\Requests\PollData;
use App\Data\Responses\PollUpdateData;
use App\Models\Session as SessionModel;
use App\Services\TelegramBotService;
use Dflydev\DotAccessData\Data;

class SendNextPollHandler implements IQuizHandler
{

    protected SessionModel $session;

    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    public function handle(TelegramBotService $botService): void
    {
        $sessionQuestions = is_string($this->session->questions_to_go) ?
            collect(json_decode($this->session->questions_to_go, true)) :
            collect($this->session->questions_to_go);

        $question = $sessionQuestions->shuffle()->first();

        $questionData = QuestionData::from([
            'text' => $question['body'],
            'answers' => $question['answers'],
            'correctAnswer' => $question['correct_answer'],
        ]);

        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll(PollData::from([
            'chatId' => $this->session->chat_id,
            'text' => $questionData->text,
            'options' => $questionData->answers,
            'correctOptionId' => (int) $questionData->correctAnswer,
            'openPeriod' => (int) config('telegramBot.time_to_answer'),
        ]));

        $this->session->update([
            'questions_to_go' => json_encode($updatedQuestionsToGo),
            'current_question' => json_encode([
                'question' => $questionData->text,
                'correctId' => $poll->getCorrectOptionId(),
            ]),
            'poll_id' => $poll->getPollId()
        ]);
    }

}
