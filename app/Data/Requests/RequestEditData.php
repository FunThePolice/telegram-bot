<?php

namespace App\Data\Requests;

use App\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class RequestEditData extends Data implements ITelegramRequest
{

    public int $messageId;

    public string $messageText;

    public string $method = 'GET';

    public string $uri = 'editMessageText';

    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => config('telegramBot.channel_id'),
                'message_id' => $this->messageId,
                'text' => $this->messageText,
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

    public function getMessageText(): string
    {
        return $this->messageText;
    }
}
