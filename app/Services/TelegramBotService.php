<?php

namespace App\Services;

use App\Concerns\HandlesResponse;
use App\Data\Contracts\ITelegramRequest;
use App\Data\Contracts\ITelegramResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Event\RuntimeException;

class TelegramBotService
{
use HandlesResponse;
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function sendRequest(ITelegramRequest $requestData)
    {
        $response = $this->client->request(
            $requestData->getMethod(),
            $requestData->getUri(),
            $requestData->getQuery()
        );

        if ($response->getStatusCode() == 200) {
            $responseData = collect(json_decode($response->getBody(), true));

            if (!is_null($responseData['result']) && !empty($responseData['result'])) {
                return collect($responseData['result']);
                }
            }

        throw new RuntimeException('Bad Response: update is empty');
    }

    public function getUpdates(ITelegramRequest $requestData): ITelegramResponse
    {
        $result = $this->sendRequest($requestData);
        return $this->handleResponse($result);
    }

    public function sendMessage(ITelegramRequest $requestData)
    {
        return $this->sendRequest($requestData);
    }

    public function sendPhoto(ITelegramRequest $requestData)
    {
        return $this->sendRequest($requestData);
    }

    public function answerCallbackQuery(ITelegramRequest $requestData)
    {
        return $this->sendRequest($requestData);
    }

    public function editMessageText(ITelegramRequest $requestData)
    {
        return $this->sendRequest($requestData);
    }

    public function sendPoll(ITelegramRequest $requestData)
    {
        return $this->sendRequest($requestData);
    }
}
