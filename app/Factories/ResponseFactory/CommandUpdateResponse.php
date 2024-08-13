<?php

namespace App\Factories\ResponseFactory;

use App\Concerns\getsLastUpdateId;
use App\Contracts\IUpdateResponse;
use App\Data\Responses\CommandUpdateData;
use Illuminate\Support\Collection;

class CommandUpdateResponse implements IUpdateResponse
{
    use getsLastUpdateId;

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): CommandUpdateData
    {
        $message = $this->result['message'];

        return CommandUpdateData::from([
            'updateId' => $this->result['update_id'] ?? $this->getLastUpdateId(),
            'chatId' => $message['chat']['id'] ?? '',
            'text' => $message['text'] ?? '',
            'type' => collect($message['entities'])->first()['type'],
            'messageId' => $message['message_id'] ?? 0,
            'senderName' => $message['from']['first_name'] ?? '',
            'senderId' => $message['from']['id'] ?? 0,
        ]);
    }

}
