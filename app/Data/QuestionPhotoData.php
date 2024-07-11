<?php

namespace App\Data;

use App\Data\Contracts\ITelegramRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionPhotoData extends Data implements ITelegramRequest
{

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

    public function getAnswers()
    {

    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPhotoPath(): string
    {
        return __DIR__ . '/../../storage/app/storage/images' . $this->photo;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getReplyMarkUp(Collection $answers): bool|string
    {
        $filtered = $answers->filter(function ($answer) {
            return $answer['text'] != null;
        });

        $firstLayer = [];
        $secondLayer = [];
        foreach ($filtered as $answer) {

            if (count($firstLayer) < 2) {
                $firstLayer[] = [
                    'text' => $answer['text'],
                    'callback_data' => $answer['true'] ?? '0'
                ];
            } else {
                $secondLayer[] = [
                    'text' => $answer['text'],
                    'callback_data' => $answer['true'] ?? '0'
                ];
            }
        }

        return json_encode([
            'inline_keyboard' => collect([$firstLayer, $secondLayer])->filter()
        ]);
    }
}
