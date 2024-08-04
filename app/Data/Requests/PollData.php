<?php

namespace App\Data\Requests;

use App\Contracts\ITelegramRequest;
use App\Data\OptionData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PollData extends Data implements ITelegramRequest
{

    public string $method = 'GET';

    public string $text;

    public bool $isClosed = false;

    public bool $isAnonymous = false;

    public bool $silentNotifications = true;

    public int $openPeriod = 10;

    public string $chatId;

    public int $correctOptionId;

    /** @var Collection<OptionData> */
    public Collection $options;

    public string $type = 'quiz';

    public string $uri = 'sendPoll';


    public function getQuery(): array
    {
        return [
            'query' => [
                'chat_id' => $this->chatId,
                'question' => $this->text,
                'options' => $this->getOptions(),
                'type' => $this->type,
                'correct_option_id' => $this->correctOptionId,
                'is_anonymous' => $this->isAnonymous,
                'open_period' => $this->openPeriod,
                'is_closed' => $this->isClosed,
                'disable_notification' => $this->silentNotifications,
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

    public function getOptions(): string
    {
        $options = $this->options->map(function ($option) {
            $queryOptions[] = $option->getText();
            return $queryOptions;
        });

        return json_encode($options->flatten()->toArray());
    }
}
