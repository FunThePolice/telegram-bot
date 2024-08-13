<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Contracts\ITelegramResponse;
use App\Data\QuestionData;
use App\Data\Requests\PollData;
use App\Data\Requests\QuizData;
use App\Data\Responses\PollUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Session as SessionModel;
use App\Services\TelegramBotService;
use Illuminate\Support\Collection;

class SendNextPollHandler implements IQuizHandler
{

    protected SessionModel $session;

    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    /**
     * @throws InvalidResponseTypeException
     * @throws UpdateIsEmptyException
     */
    public function handle(TelegramBotService $botService): void
    {
        $sessionQuestions = collect($this->session->getQuestionsToGo());
        $question = $sessionQuestions->shuffle()->first();

        $questionData = QuestionData::from([
            'text' => $question['body'] ?? '',
            'answers' => $question['answers'] ?? [],
            'correctAnswerIds' => $question['correct_answer'] ?? [],
        ]);

        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll($this->getPollData($questionData));

        $this->session->update([
            'questions_to_go' => $updatedQuestionsToGo,
            'current_question' => [
                'question' => $questionData->getText(),
                'correctId' => $questionData->getCorrectAnswerIds(),
            ],
            'poll_id' => $poll->getPollId()
        ]);
    }

    protected function getPollData(QuestionData $questionData): QuizData|PollData
    {
         return Collection::make($questionData->getCorrectAnswerIds())->count() > 1 ?
             PollData::from([
                 'chatId' => $this->session->getChatId(),
                 'text' => $questionData->getText(),
                 'options' => $questionData->getOptions(),
                 'correctOptionIds' => $questionData->getcorrectAnswerIds(),
                 'openPeriod' => (int) config('telegramBot.time_to_answer'),
             ]) :
             QuizData::from([
                 'chatId' => $this->session->getChatId(),
                 'text' => $questionData->getText(),
                 'options' => $questionData->getOptions(),
                 'correctOptionIds' => $questionData->getcorrectAnswerIds(),
                 'openPeriod' => (int) config('telegramBot.time_to_answer'),
             ]);
    }
}
