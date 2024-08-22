<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\IUpdateHandlerFactory;
use App\Contracts\ITelegramResponse;
use App\Contracts\IUpdateHandler;
use App\Data\Responses\CommandUpdateData;
use App\Data\Responses\PollAnswerData;
use App\Exceptions\InvalidResponseTypeException;
use App\Repositories\AnswerRepository;
use App\Services\CommandService;
use Illuminate\Contracts\Container\BindingResolutionException;


class UpdateHandlerFactory implements IUpdateHandlerFactory
{

    /**
     * @throws InvalidResponseTypeException
     * @throws BindingResolutionException
     */
    public function createHandler(ITelegramResponse $update): ?IUpdateHandler
    {

        if ($update instanceof CommandUpdateData) {
            return new CommandHandler(
                app()->makeWith(CommandService::class, ['commandData' => $update])
            );
        }

        if ($update instanceof PollAnswerData) {
            return new PollAnswerHandler(new AnswerRepository(), $update);
        }

        throw new InvalidResponseTypeException();
    }

}
