<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Data\Responses\PollAnswerData;
use App\Models\Answer;
use App\Models\Session;
use App\Services\AnswerService;
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
        /** @var AnswerService $answerService */
        $answerService = app(AnswerService::class);
        $answerService->create($this->answerData);
    }

}
