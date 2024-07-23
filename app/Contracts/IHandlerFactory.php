<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface IHandlerFactory
{

    public function createHandler(ITelegramResponse $update);

}
