<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandlerFactory;
use App\Contracts\ITelegramResponse;
use App\Contracts\IUpdateHandler;
use App\Data\Responses\CallbackUpdateData;
use App\Data\Responses\CommandUpdateData;
use App\Data\Responses\PollAnswerData;
use App\Exceptions\InvalidResponseTypeException;


class UpdateHandlerFactory implements IUpdateHandlerFactory
{

    /**
     * @throws InvalidResponseTypeException
     */
    public function createHandler(ITelegramResponse $update): ?IUpdateHandler
    {

        if ($update instanceof CallbackUpdateData) {
            return new CallbackHandler($update);
        }

        if ($update instanceof CommandUpdateData) {
            return new CommandHandler($update);
        }

        if ($update instanceof PollAnswerData) {
            return new PollAnswerHandler($update);
        }

        throw new InvalidResponseTypeException();
    }

}
