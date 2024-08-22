<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Services\SessionService;

class FinishQuizHandler implements IQuizHandler
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
    public function handle(): void
    {
        $this->sessionService->finishQuiz();
    }

}
