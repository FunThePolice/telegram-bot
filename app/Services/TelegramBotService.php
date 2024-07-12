<?php

namespace App\Services;

use App\Data\Contracts\ITelegramRequest;
use App\Data\CallbackUpdateData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use PHPUnit\Event\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class TelegramBotService
{

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
                return collect($responseData['result'])->first();
                }
            }

        throw new RuntimeException('Bad Response');
    }

    public function getUpdates(ITelegramRequest $requestData): CallbackUpdateData
    {
        $result = $this->sendRequest($requestData);

        $callback = $result['callback_query'];

        return CallbackUpdateData::from([
            'updateId' => $result['update_id'],
            'userName' => $callback['from']['first_name'],
            'userId' => $callback['from']['id'],
            'callbackData' => $callback['data'],
            'messageId' => $callback['message']['message_id'],
            'callbackQueryId' => $callback['id'],
            'replyMarkup' => $callback['message']['reply_markup'],
            'messageText' => $callback['message']['text'],
        ]);
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
}
