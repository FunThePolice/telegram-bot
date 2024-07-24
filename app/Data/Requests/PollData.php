<?php

namespace App\Data\Requests;

use App\Concerns\GetsCorrectAnswerId;
use App\Contracts\ITelegramRequest;
use App\Data\OptionData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PollData extends Data implements ITelegramRequest
{
    use GetsCorrectAnswerId;

    public string $method = 'GET';

    public string $text;

    public bool $isClosed = false;

    public bool $isAnonymous = false;

    public bool $silentNotifications = true;

    public int $openPeriod = 10;

    public string $chatId;

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
                'correct_option_id' => $this->getCorrectAnswerId($this->options),
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
//        dd($options->flatten()->toArray());
//dd(implode(',', $options->flatten()->toArray()));
        return json_encode($options->flatten()->toArray());
    }
}
