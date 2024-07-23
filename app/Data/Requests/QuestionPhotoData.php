<?php

namespace App\Data\Requests;

use App\Concerns\GetsReplyMarkup;
use App\Contracts\ITelegramRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionPhotoData extends Data implements ITelegramRequest
{
    use GetsReplyMarkup;

    public string $text;

    public string $photo;

    public Collection $answers;

    public string $method = 'POST';

    public string $uri = 'sendPhoto';


    public function getQuery(): array
    {
        return [
            'multipart' => [
                [
                    'name' => 'chat_id',
                    'contents' => config('telegramBot.channel_id')
                ],
                [
                    'name' => 'caption',
                    'contents' => $this->getText()
                ],
                [
                    'name' => 'photo',
                    'contents' => fopen($this->getPhotoPath(), 'r')
                ],
                [
                    'name' => 'reply_markup',
                    'contents' => $this->getReplyMarkUp($this->answers)
                ]
            ]
        ];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPhotoPath(): string
    {
        return config('telegramBot.images_path') . $this->photo;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

}
