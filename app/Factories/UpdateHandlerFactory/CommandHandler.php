<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandler;
use App\Services\CommandService;
use App\Services\TelegramBotService;

class CommandHandler implements IUpdateHandler
{

    protected CommandService $commandService;

    public function __construct(CommandService $commandService)
    {
        $this->commandService = $commandService;
    }

    public function handle(): void
    {
        $this->commandService->processCommand();
    }

}
