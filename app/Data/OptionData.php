<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class OptionData extends Data
{

    public string $text;

    public bool $true = false;

    public function getText(): string
    {
        return $this->text;
    }

    public function isCorrect(): bool
    {
        return $this->true;
    }

}
