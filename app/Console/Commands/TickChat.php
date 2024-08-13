<?php

namespace App\Console\Commands;

use App\Data\Requests\RequestUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Factories\UpdateHandlerFactory\UpdateHandlerFactory;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TickChat extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tick-chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends request for updates from telegram, processes given result based on result type';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $botService): void
    {
        try {
            $updateResponse = $botService->getUpdates(
                new RequestUpdateData(['callback_query','message','poll_answer'])
            );
        } catch (UpdateIsEmptyException|InvalidResponseTypeException $e) {
            return;
        }

        $handlerFactory = new UpdateHandlerFactory();

        try {
            $handlerFactory->createHandler($updateResponse)->handle($botService);
        } catch (InvalidResponseTypeException $e) {
            return;
        }

    }

}
