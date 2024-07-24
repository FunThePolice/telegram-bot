<?php

namespace App\Data\Requests;

use App\Contracts\ITelegramRequest;
use Spatie\LaravelData\Data;

class RequestUpdateData extends Data implements ITelegramRequest
{

    public array $allowedUpdates;

    public string $method = 'GET';

    public string $uri = 'getUpdates';

    public function __construct(array $updateType = [])
    {
        $this->allowedUpdates = $updateType;
    }

    public function getQuery(): array
    {
        return [
            'query' => [
                'offset' => (int) file_get_contents(config('telegramBot.cursorPath')),
                'allowed_updates' => $this->getQueryAllowedUpdates(),
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

    protected function getQueryAllowedUpdates(): string
    {
        return json_encode($this->allowedUpdates);
    }
}
