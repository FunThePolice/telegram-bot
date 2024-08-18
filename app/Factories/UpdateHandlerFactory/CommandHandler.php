<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Data\Requests\MessageTextData;
use App\Data\Responses\CommandUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Question;
use App\Models\Score;
use App\Models\Session;
use App\Services\ScoreService;
use App\Services\TelegramBotService;

class CommandHandler implements IUpdateHandler
{

    protected CommandUpdateData $commandData;

    public function __construct(CommandUpdateData $commandData)
    {
        $this->commandData = $commandData;
    }

    public function handle(TelegramBotService $botService): void
    {
        $command = $this->commandData->getText();

        if ($command === '/start' . config('telegramBot.bot_name')) {
            $this->handleStartCommand();
        }

        if ($command === '/top' . config('telegramBot.bot_name')) {
            $this->handleTopCommand($botService);
        }

    }

    protected function handleStartCommand(): void
    {
        Session::create([
            'chat_id' => $this->commandData->getChatId(),
            'questions_to_go' => Question::pluck('id'),
        ]);
    }

    protected function handleTopCommand(TelegramBotService $botService): void
    {
        /** @var ScoreService $scoreService */
        $scoreService = app(ScoreService::class);
        $message = $scoreService->getChatTopScoresRequest($this->commandData->getChatId());

        try {
            $botService->sendMessage($message);
        } catch (UpdateIsEmptyException|InvalidResponseTypeException $e) {
            return;
        }
    }

}
