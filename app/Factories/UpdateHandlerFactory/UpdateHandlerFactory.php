<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandlerFactory;
use App\Contracts\ITelegramResponse;
use App\Contracts\IUpdateHandler;
use App\Data\Responses\CallbackUpdateData;
use App\Data\Responses\CommandUpdateData;
use App\Data\Responses\PollAnswerData;


class UpdateHandlerFactory implements IUpdateHandlerFactory
{

    public function createHandler(ITelegramResponse $update): ?IUpdateHandler
    {
        $handler = null;

        if ($update instanceof CallbackUpdateData) {
            $handler = new CallbackHandler($update);
        }

        if ($update instanceof  CommandUpdateData) {
            $handler = new CommandHandler($update);
        }

        if ($update instanceof PollAnswerData) {
            $handler = new PollAnswerHandler($update);
        }

        return $handler;
    }

}
