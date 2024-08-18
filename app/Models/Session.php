<?php

namespace App\Models;

use App\Data\Models\CurrentQuestionDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Session extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['questions_to_go' => 'array', 'current_question' => 'array'];


    public function getQuestionsToGo(): Collection
    {
        return collect($this->questions_to_go ?? []);
    }

    public function getCurrentQuestion(): CurrentQuestionDto
    {
        return CurrentQuestionDto::from([
            'questionId' => $this->current_question
        ]);
    }

    public function getChatId(): string
    {
        return $this->chat_id ?? '';
    }

}
