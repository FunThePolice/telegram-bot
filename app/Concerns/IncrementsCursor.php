<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

trait IncrementsCursor
{

    public function incrementCursor(array $result): void
    {
        if (collect($result)->has('update_id')) {
            file_put_contents(config('telegramBot.cursorPath'), $result['update_id'] + 1);
        }
    }

}
