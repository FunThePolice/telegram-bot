<?php

namespace App\Data;

use App\Data\Contracts\ITelegramRequest;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class QuestionTextData extends Data implements ITelegramRequest
{

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
