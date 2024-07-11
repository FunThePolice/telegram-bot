<?php

namespace App\Services;

use App\Data\Contracts\ITelegramRequest;
use App\Data\CallbackUpdateData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
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
    public function sendRequest(ITelegramRequest $requestData): Collection|CallbackUpdateData|bool
    {
        $response = $this->client->request(
            $requestData->getMethod(),
            $requestData->getUri(),
            $requestData->getQuery()
        );

        $result = Collection::make();
        if ($response->getStatusCode() == 200) {
            return $this->processResponse($response);
        }
        return $result;
    }

    public function processResponse(ResponseInterface $response): CallbackUpdateData|Collection|bool
    {
        $responseData = collect(json_decode($response->getBody()->getContents(), true));

        $result = Collection::make();

        if ($responseData->has('result') && !empty($responseData['result'])) {
            $data = $responseData['result'];

            if (is_bool($data)) {
                return true;
            }

            $result = collect(array_shift($data));

            switch ($result) {
                case ($result->has('callback_query')):

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

                    case ($result->has('message')):
                        return $responseData['result']['message_id'];
            }
            return $result;
        }

        return $result;
    }

    public function sendControlMessage(): void
    {
        $this->client->request('GET', 'sendMessage' , [
            'query' => [
                'chat_id' => config('telegramBot.channel_id'),
                'text' => 'This is control message',
                'reply_markup' => json_encode([
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "Start",
                                "callback_data" => "start"
                            ],
                            [
                                "text" => "Top",
                                "callback_data" => "top"
                            ],
                            [
                                "text" => "Finish",
                                "callback_data" => "finish"
                            ]
                        ]
                    ]
                ])
            ]
        ]);
    }

}
