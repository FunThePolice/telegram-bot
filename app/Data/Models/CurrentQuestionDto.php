<?php

namespace App\Data\Models;

use Spatie\LaravelData\Data;

class CurrentQuestionDto extends Data
{
    public array $correctIds;
    public string $question;

    public function getCorrectIds(): array
    {
        return array_filter($this->correctIds, 'intval');
    }
}
