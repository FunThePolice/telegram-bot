<?php

namespace App\Data\Responses;

use App\Contracts\ITelegramResponse;
use Spatie\LaravelData\Data;

class CommandUpdateData extends Data implements ITelegramResponse
{

    public string $chatId;

    public string $text;

    public string $type;

    public int $messageId;

    public int $updateId;

    public string $senderName;

    public int $senderId;

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getUpdateId(): int
    {
        return $this->updateId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

}
