<?php

namespace App\Http\Resources;

use App\Http\Requests\QuestionRequest;
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
            'answers' => collect($this->answers)
                ->filter(function ($answer) {
                return $answer['text'] != null;
            })->toJson(),
            'correct_answer' => $this->getCorrectAnswer($request)
        ];
    }

    public function getCorrectAnswer(Request $request)
    {
        $answer = [];
        foreach ($request->input('answers') as $item) {
            if (is_array($item) && count($item) > 1) {
                $answer = $item;
            }
        }

        return $answer['text'];
    }
}
