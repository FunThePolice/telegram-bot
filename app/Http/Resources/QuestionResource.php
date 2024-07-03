<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'body' => $this->text,
            'answer_1' => $this->answer_1['text'],
            'answer_2' => $this->answer_2['text'],
            'answer_3' => $this->answer_3['text'],
            'answer_4' => $this->answer_4['text'],
            'correct_answer' => $this->getCorrectAnswer($request)
        ];
    }

    public function getCorrectAnswer(Request $request)
    {
        foreach ($request->all() as $item) {
            if (is_array($item) && count($item) > 1) {
                $answer = $item;
            }
        }
        return $answer['text'];
    }
}
