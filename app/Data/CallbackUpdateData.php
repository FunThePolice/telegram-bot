<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CallbackUpdateData extends Data
{

    public int $updateId;

    public string $userName;

    public int $userId;

    public string $callbackData;

    public int $messageId;

    public int $callbackQueryId;

    public string $replyMarkUp;

    public string $messageText;

    public function getUpdateId(): int
    {
        return $this->updateId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCallbackData(): int|string
    {
        return match ($this->callbackData) {
            '0' => 0,
            '1' => 1,
            default => $this->callbackData,
        };
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getCallbackQueryId(): int
    {
        return $this->callbackQueryId;
    }

    public function getReplyMarkUp(): string
    {
        return $this->replyMarkUp;
    }

    public function getMessageText(): string
    {
        return $this->messageText;
    }

}
