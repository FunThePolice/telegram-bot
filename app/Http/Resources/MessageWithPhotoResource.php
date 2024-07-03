<?php

namespace App\Http\Resources;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageWithPhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            [
                'name' => 'caption',
                'contents' => $this->text
            ],
            [
                'name' => 'photo',
                'contents' => fopen($this->image->path(), 'r')
            ],
            [
                'name' => 'reply_markup',
                'contents' => json_encode([
                    'inline_keyboard' => [
                        array(
                            array(
                                'text' => $this->answer_1['text'],
                                'callback_data' => '/'.($this->answer_1['true'] ?? '0'),
                            ),

                            array(
                                'text' => $this->answer_2['text'],
                                'callback_data' => '/'.($this->answer_2['true'] ?? '0'),
                            )
                        ),
                        array(
                            array(
                                'text' => $this->answer_3['text'],
                                'callback_data' => '/'.($this->answer_3['true'] ?? '0'),
                            ),

                            array(
                                'text' => $this->answer_4['text'],
                                'callback_data' => '/'.($this->answer_4['true'] ?? '0'),
                            ),
                        )
                    ]
                ])
            ]
        ];
    }
}
