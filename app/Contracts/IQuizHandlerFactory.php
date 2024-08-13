<?php

namespace App\Contracts;

use App\Models\Session as SessionModel;
use App\Services\Poll;

interface IQuizHandlerFactory
{

    public function createHandler(Poll $session);

}
