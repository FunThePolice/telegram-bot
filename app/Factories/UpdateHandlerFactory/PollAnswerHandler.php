<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Data\Responses\PollAnswerData;
use App\Repositories\AnswerRepository;
use App\Services\TelegramBotService;

class PollAnswerHandler implements IUpdateHandler
{

    protected PollAnswerData $answerData;

    public function __construct(PollAnswerData $answerData)
    {
        $this->answerData = $answerData;
    }

    public function handle(TelegramBotService $botService): void
    {
        /** @var AnswerRepository $answerRepository */
        $answerRepository = app(AnswerRepository::class);
        $answerRepository->create($this->answerData);
    }

}
