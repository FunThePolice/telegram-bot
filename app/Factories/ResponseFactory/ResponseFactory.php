<?php

namespace App\Factories\ResponseFactory;

use App\Concerns\IncrementsCursor;
use App\Contracts\IUpdateResponse;
use App\Contracts\IResponseFactory;
use Illuminate\Support\Collection;

class ResponseFactory implements IResponseFactory
{
    use IncrementsCursor;
    public function createResponse(Collection $result): ?IUpdateResponse
    {
        $response = null;

        if ($result->has('callback_query')) {
            $response = new CallbackUpdateResponse($result);
        }

        if ($result->has('message')) {

            $response = $this->isCommand($result) ?
                new CommandUpdateResponse($result) :
                new MessageUpdateResponse($result);
        }

        if ($result->has('poll_answer')) {
            $response = new PollAnswerUpdateResponse($result);
        }

        if ($result->has('poll')) {
            $response = new PollUpdateResponse($result);
        }

        if ($result->has('text')) {
            $response = new MessageUpdateResponse($result);
        }

        return $response;
    }

    protected function isCommand(Collection $result): bool
    {
        $entities = collect($result['message']['entities']);

        return $entities->contains(function ($value) {
            return isset($value['type']) && $value['type'] === 'bot_command';
        });
    }

}
