<?php

namespace App\Data;

use App\Data\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class TelegramRequestEditData extends Data implements ITelegramRequest
{

    public int $messageId;

    public string $replyMarkUp;

    public string $messageText;

    public string $method = 'GET';

    public string $uri = 'editMessageText';

    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => config('telegram.channel_id'),
                'message_id' => $this->messageId,
                'text' => $this->messageText,
                'reply_markup' => $this->replyMarkUp
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
