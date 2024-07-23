<?php

namespace App\Services;

use App\Concerns\IncrementsCursor;
use App\Contracts\ITelegramRequest;
use App\Contracts\ITelegramResponse;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Factories\ResponseFactory\ResponseFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    use IncrementsCursor;
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws UpdateIsEmptyException
     * @throws InvalidResponseTypeException
     */
    public function sendRequest(ITelegramRequest $requestData): ?ITelegramResponse
    {
        try {
            $response = $this->client->request(
                $requestData->getMethod(),
                $requestData->getUri(),
                $requestData->getQuery()
            );
        } catch (GuzzleException $e) {
            Log::info('Мы упали: ' . $e->getMessage() . 'код:' . $e->getCode() . $e->getTraceAsString());
            return null;
        }

        if ($response->getStatusCode() == 200) {
            $responseData = collect(json_decode($response->getBody(), true));

            if (!is_null($responseData['result']) && !empty($responseData['result'])) {
                $result = $this->isUpdate($responseData['result']) ?
                    collect($responseData['result'])->first() :
                    $responseData['result'];

                $this->incrementCursor($result);
                return $this->handleResponse(collect($result));
                }
            }

        throw new UpdateIsEmptyException();
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function getUpdates(ITelegramRequest $requestData): ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function sendMessage(ITelegramRequest $requestData): ?ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function sendPhoto(ITelegramRequest $requestData): ?ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function answerCallbackQuery(ITelegramRequest $requestData): ?ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function editMessageText(ITelegramRequest $requestData): ?ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws UpdateIsEmptyException|InvalidResponseTypeException
     */
    public function sendPoll(ITelegramRequest $requestData): ?ITelegramResponse
    {
        return $this->sendRequest($requestData);
    }

    /**
     * @throws InvalidResponseTypeException
     */
    protected function handleResponse(Collection $result): ?ITelegramResponse
    {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse($result);

        if (is_null($response)) {
            throw new InvalidResponseTypeException();
        }

        return $response->create();
    }

    protected function isUpdate(array $result): bool
    {
        return is_array(collect($result)->first());
    }

}
