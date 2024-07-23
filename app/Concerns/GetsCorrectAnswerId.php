<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

trait GetsCorrectAnswerId
{

    public function getCorrectAnswerId(Collection $options)
    {
        return $options->map(function ($option) {
            return $option->IsCorrect();
        })
            ->values()->search(function ($value) {
            return $value === true;
        });
    }
}
