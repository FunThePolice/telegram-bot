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
use App\Models\Question;
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
//        $sessionQuestions = $this->session->getQuestionsToGo();
//        $question = $sessionQuestions->shuffle()->first();

//        $questionData = QuestionData::from([
//            'text' => $question['body'] ?? '',
//            'answers' => $question['answers'] ?? [],
//            'correctAnswerIds' => $question['correct_answer'] ?? [],
//        ]);
        //$questionData = QuestionData::from($question->toArray());

        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));
        $poll->forgetQuestion($question);

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll($this->getPollData($question));

        $this->session->update([
            'questions_to_go' => $poll->getQuestionsToGo()->pluck('id'),
            'current_question' => $question->id,
            'poll_id' => $poll->getQuestion()->poll_id
        ]);
    }

    protected function getPollData(Question $question): QuizData|PollData
    {
         return Collection::make($question->getCorrectAnswerIds())->count() > 1 ?
             PollData::from([
                 'chatId' => $this->session->getChatId(),
                 'text' => $question->body,
                 'options' => $question->getOptions(),
                 'correctOptionIds' => $question->getcorrectAnswerIds(),
                 'openPeriod' => (int) config('telegramBot.time_to_answer'),
             ]) :
             QuizData::from([
                 'chatId' => $this->session->getChatId(),
                 'text' => $question->getText(),
                 'options' => $question->getOptions(),
                 'correctOptionIds' => $question->getcorrectAnswerIds(),
                 'openPeriod' => (int) config('telegramBot.time_to_answer'),
             ]);
    }
}
