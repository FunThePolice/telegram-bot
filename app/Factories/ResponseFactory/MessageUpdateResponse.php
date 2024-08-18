<?php

namespace App\Factories\ResponseFactory;

use App\Concerns\GetsLastUpdateId;
use App\Contracts\IUpdateResponse;
use App\Data\Responses\MessageUpdateData;
use Illuminate\Support\Collection;

class MessageUpdateResponse implements IUpdateResponse
{
    use GetsLastUpdateId;

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
            'updateId' => $this->result['update_id'] ?? $this->getLastUpdateId(),
            'chatId' => $message['chat']['id'] ?? '',
            'text' => $message['text'] ?? '',
            'messageId' => $message['message_id'] ?? 0,
            'senderName' => $message['from']['first_name'] ?? '',
            'senderId' => $message['from']['id'] ?? 0,
        ]);
    }

}
