<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

trait GetsReplyMarkup
{

    public function getReplyMarkUp(Collection $answers): string
    {
        $filtered = $answers->filter(function ($answer) {
            return $answer['text'] !== null;
        });

        $firstLayer = [];
        $secondLayer = [];
        foreach ($filtered as $answer) {

            if (count($firstLayer) < 2) {
                $firstLayer[] = [
                    'text' => $answer['text'],
                    'callback_data' => $answer['true'] ?? '0'
                ];
            } else {
                $secondLayer[] = [
                    'text' => $answer['text'],
                    'callback_data' => $answer['true'] ?? '0'
                ];
            }

        }
        return json_encode([
            'inline_keyboard' => collect([$firstLayer, $secondLayer])->filter()
        ]);
    }

}
