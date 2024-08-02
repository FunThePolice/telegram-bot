<?php

namespace App\Contracts;

use App\Services\TelegramBotService;

interface IQuizHandler
{

    public function handle(TelegramBotService $botService);

}
