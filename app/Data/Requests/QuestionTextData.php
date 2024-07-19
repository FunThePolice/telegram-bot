<?php

namespace App\Data\Requests;

use App\Concerns\GetsReplyMarkup;
use App\Data\Contracts\ITelegramRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionTextData extends Data implements ITelegramRequest
{
    use GetsReplyMarkup;

    public Collection $answers;

    public string $text;

    public string $method = 'GET';

    public string $uri = 'sendMessage';

    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => config('telegramBot.channel_id'),
                'text' => $this->text,
                'reply_markup' => $this->getReplyMarkUp($this->answers)
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
