<?php

namespace App\Data;

use App\Data\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class MessageTextData extends Data implements ITelegramRequest
{

    public string $method = 'GET';

    public string $uri = 'sendMessage';

    public string $text;

    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => config('telegramBot.channel_id'),
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
