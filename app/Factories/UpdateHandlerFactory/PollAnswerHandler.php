<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Data\Responses\PollAnswerData;
use App\Models\Answer;
use App\Models\Session;
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
        $session = Session::where('poll_id', $this->answerData->getPollId())->get()->first();
        $isCorrect = $session->getCurrentQuestion()['correctId'] == $this->answerData->getOptionIds()->toArray();
        dump($isCorrect, $session->getCurrentQuestion()['correctId'], $this->answerData->getOptionIds()->toArray());
        Answer::create([
            'chat_id' => $session->getChatId(),
            'user_name' => $this->answerData->getUserName(),
            'user_id' => $this->answerData->getUserId(),
            'is_correct' => $isCorrect,
        ]);
    }

}
