<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Data\Responses\PollAnswerData;
use App\Repositories\AnswerRepository;
use App\Services\TelegramBotService;

class PollAnswerHandler implements IUpdateHandler
{

    protected AnswerRepository $answerRepository;

    protected PollAnswerData $answerData;

    public function __construct(AnswerRepository $answerRepository, PollAnswerData $answerData)
    {
        $this->answerRepository = $answerRepository;
        $this->answerData = $answerData;
    }

    public function handle(): void
    {
        $this->answerRepository->create($this->answerData);
    }

}
