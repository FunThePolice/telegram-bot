<?php

namespace App\Console\Commands;

use App\Contracts\ITelegramRequest;
use App\Contracts\ITelegramResponse;
use App\Data\Requests\CallbackAnswerData;
use App\Data\Requests\MessageTextData;
use App\Data\Requests\QuestionPhotoData;
use App\Data\Requests\QuestionTextData;
use App\Data\Requests\RequestEditData;
use App\Data\Requests\RequestUpdateData;
use App\Data\Responses\CallbackUpdateData;
use App\Data\Responses\CommandUpdateData;
use App\Data\Responses\PollAnswerData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Factories\UpdateHandlerFactory\UpdateHandlerFactory;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Score;
use App\Models\Session;
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
    public function handle(): void
    {
        $botService = app(TelegramBotService::class);

        try {
            $updateResponse = $botService->getUpdates(
                new RequestUpdateData(['callback_query','message','poll_answer'])
            );
        } catch (UpdateIsEmptyException $e) {
            return;
        }

        $handlerFactory = new UpdateHandlerFactory();
        $handlerFactory->createHandler($updateResponse)->handle($botService);
    }

}
