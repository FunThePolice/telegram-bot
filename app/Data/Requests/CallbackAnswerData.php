<?php

namespace App\Data\Requests;

use App\Data\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class CallbackAnswerData extends Data implements ITelegramRequest
{

    public string $method = "GET";

    public string $uri = 'answerCallbackQuery';

    public int $callbackId;

    public bool $isCorrect;

    public string $userName;

    public function getQuery(): array
    {
        return [
            'query' => [
                'callback_query_id' => $this->callbackId,
                'text' => $this->getCallbackText(),
                ]
            ];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getCallbackText(): string
    {
        return $this->isCorrect ?
            sprintf('%s: Right answer!', $this->userName):
            sprintf('%s: Wrong answer!', $this->userName);
    }

}
