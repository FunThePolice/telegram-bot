<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface IResponseFactory
{

    public function createResponse(Collection $result);

}
