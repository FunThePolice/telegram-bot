<?php

namespace App\Concerns;

trait getsLastUpdateId
{

    public function getLastUpdateId(): ?int
    {
        return file_get_contents(config('telegramBot.cursorPath')) ?? null;
    }

}
