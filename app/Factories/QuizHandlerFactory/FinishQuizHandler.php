<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Data\Requests\MessageTextData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Answer;
use App\Models\Score;
use App\Models\Session as SessionModel;
use App\Services\ScoreService;
use App\Services\SessionService;
use App\Services\TelegramBotService;

class FinishQuizHandler implements IQuizHandler
{

    protected SessionService $session;

    public function __construct(SessionService $session)
    {
        $this->session = $session;
    }

    /**
     * @throws InvalidResponseTypeException
     * @throws UpdateIsEmptyException
     */
    public function handle(TelegramBotService $botService): void
    {
        /** @var ScoreService $scoreService */
        $scoreService = app(ScoreService::class);
        $scoreService->saveScores($this->session->getSession()->getChatId());

        $botService->sendMessage($scoreService->getChatTopScoresRequest($this->session->getSession()->getChatId()));

        Answer::where('chat_id', $this->session->getSession()->getChatId())->delete();
        $this->session->getSession()->delete();
    }

}
