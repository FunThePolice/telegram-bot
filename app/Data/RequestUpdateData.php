<?php

namespace App\Data;

use App\Data\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class RequestUpdateData extends Data implements ITelegramRequest
{

    public string $allowedUpdate;

    public string $method = 'GET';

    public string $uri = 'getUpdates';

    public function __construct(string $updateType = '')
    {
        $this->allowedUpdate = $updateType;
    }

    public function getQuery(): array
    {
        return [
            'query' => [
                'offset' => (int) file_get_contents(config('telegramBot.cursorPath')),
                'allowed_updates' => json_encode($this->allowedUpdate),
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
