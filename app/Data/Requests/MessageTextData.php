<?php

namespace App\Data\Requests;

use App\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class MessageTextData extends Data implements ITelegramRequest
{

    public string $method = 'GET';

    public string $uri = 'sendMessage';

    public string $text;

    public string $chatId;

    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => $this->chatId,
                'text' => $this->text,
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

}
