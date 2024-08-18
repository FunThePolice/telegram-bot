<?php

namespace App\Concerns;

trait GetsLastUpdateId
{

    public function getLastUpdateId(): ?int
    {
        return file_get_contents(config('telegramBot.cursorPath')) ?? null;
    }

}
