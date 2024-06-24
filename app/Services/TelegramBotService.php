<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramBotService
{

    const TELEGRAM_CHANNEL_ID = '-4194487285';

    const CURRENT_SERVER = 'https://telegram-bot.local/index.php';

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

    public function setHook()
    {
        $response = $this->client->request('GET' , 'setWebhook', [
            'query' => [
                'url' => self::CURRENT_SERVER,
            ]
        ]);
        dd($response->getBody()->getContents());
    }

}
