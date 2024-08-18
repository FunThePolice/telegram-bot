<?php

namespace App\Contracts;

use App\Models\Session as SessionModel;
use App\Services\Poll;
use App\Services\SessionService;

interface IQuizHandlerFactory
{

    public function createHandler(SessionService $sessionService);

}
