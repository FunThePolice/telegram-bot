<?php

namespace App\Contracts;

use App\Services\TelegramBotService;

interface IUpdateHandler
{

    public function handle();

}
