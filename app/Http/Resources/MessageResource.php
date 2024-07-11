<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'query' => [
                'chat_id' => config('telegramBot.channel_id'),
                'text' => $this->text,
                'reply_markup' => $this->getReplyMarkUp(collect($request['answers']))
            ]
        ];
    }

    public function getReplyMarkUp(Collection $answers): bool|string
    {
        $filtered = $answers->filter(function ($answer) {
            return $answer['text'] != null;
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
