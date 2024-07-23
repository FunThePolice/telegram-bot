<?php

namespace App\Data\Responses;

use App\Contracts\ITelegramResponse;
use Spatie\LaravelData\Data;

class PollAnswerData extends Data implements ITelegramResponse
{

    public int $pollId;

    public int $updateId;

    public string $userName;

    public int $userId;

    public int $optionId;

    public function getPollId(): int
    {
        return $this->pollId;
    }

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

    public function getOptionId(): int
    {
        return $this->optionId;
    }

}
