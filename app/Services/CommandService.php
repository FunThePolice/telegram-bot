<?php

namespace App\Services;

use App\Data\Responses\CommandUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Session;
use App\Repositories\QuestionRepository;

class CommandService
{

    protected CommandUpdateData $commandData;

    protected TelegramBotService $botService;

    protected QuestionRepository $questionRepository;

    protected ScoreService  $scoreService;

    public function __construct(
        CommandUpdateData $commandData,
        TelegramBotService $botService,
        QuestionRepository $questionRepository,
        ScoreService $scoreService
    )
    {
        $this->commandData = $commandData;
        $this->botService = $botService;
        $this->questionRepository = $questionRepository;
        $this->scoreService = $scoreService;
    }

    public function processCommand(): void
    {
        $command = $this->commandData->getText();

        if ($command === '/start' . config('telegramBot.bot_name')) {
            $this->createSession();
        }

        if ($command === '/top' . config('telegramBot.bot_name')) {
            $this->getTopScore();
        }

    }

    protected function createSession(): void
    {
        Session::create([
            'chat_id' => $this->commandData->getChatId(),
            'questions_to_go' => $this->questionRepository->getAllIds(),
        ]);
    }

    protected function getTopScore(): void
    {
        try {
            $this->botService->sendMessage(
                $this->scoreService->getChatTopScoresRequest($this->commandData->getChatId())
            );
        } catch (UpdateIsEmptyException|InvalidResponseTypeException $e) {
            return;
        }
    }
}
