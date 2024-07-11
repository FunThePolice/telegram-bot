<?php

namespace App\Data\Contracts;

interface ITelegramRequest
{

    public function getQuery(): array;

    public function getMethod(): string;

    public function getUri(): string;

}
