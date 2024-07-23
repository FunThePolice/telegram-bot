<?php

namespace App\Factories\ResponseFactory;

use App\Contracts\IBotResponse;
use App\Contracts\IUpdateResponse;
use App\Data\Responses\MessageUpdateData;
use Illuminate\Support\Collection;

class MessageUpdateResponse implements IUpdateResponse
{

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): MessageUpdateData
    {
        $message = isset($this->result['message']) ?
            $this->result['message'] :
            $this->result;

        return MessageUpdateData::from([
            'updateId' => $this->result['update_id'] ?? null,
            'chatId' => $message['chat']['id'],
            'text' => $message['text'],
            'messageId' => $message['message_id'],
            'senderName' => $message['from']['first_name'],
            'senderId' => $message['from']['id'],
        ]);
    }

}
