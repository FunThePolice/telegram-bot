<?php

namespace App\Contracts;

use App\Models\Session as SessionModel;

interface IQuizHandlerFactory
{

    public function createHandler(SessionModel $session);

}
