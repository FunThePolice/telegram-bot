<?php

namespace App\Http\Resources;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionWithPhotoResource extends JsonResource
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
                                'text' => $this->answer1['text'],
                                'callback_data' => $this->answer1['true'] ?? '0',
                            ),

                            array(
                                'text' => $this->answer2['text'],
                                'callback_data' => $this->answer2['true'] ?? '0',
                            )
                        ),
                        array(
                            array(
                                'text' => $this->answer3['text'],
                                'callback_data' => $this->answer3['true'] ?? '0',
                            ),

                            array(
                                'text' => $this->answer4['text'],
                                'callback_data' => $this->answer4['true'] ?? '0',
                            ),
                        )
                    ]
                ])
            ]
        ];
    }
}
