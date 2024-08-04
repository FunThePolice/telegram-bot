<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface IUpdateHandlerFactory
{

    public function createHandler(ITelegramResponse $update);

}
