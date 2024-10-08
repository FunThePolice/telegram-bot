<?php

namespace App\Data\Responses;

use App\Contracts\ITelegramResponse;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PollUpdateData extends Data implements ITelegramResponse
{

    public ?int $updateId;

    public int $pollId;

    public string $question;

    public Collection $options;

    public Collection $correctOptionIds;

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

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function getCorrectOptionIds(): ?Collection
    {
        return $this->correctOptionIds ?? null;
    }

}
