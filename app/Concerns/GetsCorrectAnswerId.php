<?php

namespace App\Concerns;

trait GetsCorrectAnswerId
{

    public function getCorrectAnswerId(array $options)
    {
        return collect($options)->values()
            ->filter(function ($value) {
                return count($value) > 1;
            })
            ->keys()->first();
    }
}
