<?php

namespace App\Data\Responses;

use App\Data\Contracts\ITelegramResponse;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PollUpdateData extends Data implements ITelegramResponse
{

    public int $updateId;

    public int $pollId;

    public string $question;

    public Collection $options;

    public int $correctOptionId;

    public function getUpdateId(): int
    {
        return $this->updateId;
    }

    public function getPollId(): int
    {
        return $this->pollId;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getCorrectOptionId(): int
    {
        return $this->correctOptionId;
    }
}
