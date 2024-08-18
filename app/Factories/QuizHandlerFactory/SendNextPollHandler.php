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
use App\Services\Poll;
use App\Services\PollService;
use App\Services\QuestionService;
use App\Services\SessionService;
use App\Services\TelegramBotService;
use Illuminate\Support\Collection;

class SendNextPollHandler implements IQuizHandler
{

    protected SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @throws InvalidResponseTypeException
     * @throws UpdateIsEmptyException
     */
    public function handle(TelegramBotService $botService): void
    {
        /** @var QuestionService $questionService */
        $questionService = app(QuestionService::class);
        $questionToGo = $questionService->getRandomQuestion($this->sessionService->getSession());
        $this->sessionService->forgetQuestion($questionToGo);

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll(
            (new PollService($this->sessionService->getSession()))
            ->getPollData($questionToGo)
        );

        $this->sessionService->updateSession($poll->getPollId(), $questionToGo->id);
    }

}
