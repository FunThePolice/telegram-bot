<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class TelegramBotService
{

    const TELEGRAM_CHANNEL_ID = '-4194487285';

    const PATH_TO_CURSOR = __DIR__ . '/../../resources/bot_update_id_cursor';

    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function sendMessage(array $params = [])
    {
        $params = ['chat_id' => self::TELEGRAM_CHANNEL_ID] + $params ;

        $response = $this->client->request('GET' , 'sendMessage', [
            'query' => $params
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

    }

    public function sendMessageWithPhoto(array $params = [])
    {
        $params[] = array('name' => 'chat_id', 'contents' => self::TELEGRAM_CHANNEL_ID,);

        $response = $this->client->request('POST' , 'sendPhoto', [
            'multipart' => $params
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

    }

    public function getCallback()
    {
        $response = $this->client->request('GET' , 'getUpdates', [
            'query' => [
                'offset' => (int) file_get_contents(self::PATH_TO_CURSOR),
                'allowed_updates' => json_encode(["callback_query"])
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

    }

    public function getUpdates()
    {
        $response = $this->client->request('GET' , 'getUpdates', [
            'query' => [
                'offset' => (int)file_get_contents(self::PATH_TO_CURSOR),
                'allowed_updates' => json_encode(["message"])
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

    }

    public function answerCallbackQuery(array $callbackQuery = [])
    {
        $response = $this->client->request('GET' , 'answerCallbackQuery', [
            'query' => [
                'callback_query_id' => $callbackQuery['callback_id'],
                'text' => $callbackQuery['message'],
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

    }

    public function editMessage(array $params = [])
    {
        $params = ['chat_id' => self::TELEGRAM_CHANNEL_ID] + $params ;
        $this->client->request('GET' , 'editMessageText', [
            'query' => $params
        ]);
    }

    public function prepareAnswer(Collection $params)
    {
        $from = [
            'name' => $params['callback_query']['from']['first_name'],
            'user_id' => $params['callback_query']['from']['id'],
        ];

        if ($params['callback_query']['data'] === '/1') {
            $data = [
                'message' => 'Right answer!',
                'callback_id' => $params['callback_query']['id'],
                'is_correct' => true,
            ];
        } else {
            $data = [
                'message' => 'Wrong answer!',
                'callback_id' => $params['callback_query']['id'],
                'is_correct' => false,
            ];
        }

        return array_merge($from, $data);
    }

}
