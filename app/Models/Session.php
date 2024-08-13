<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['questions_to_go' => 'array', 'current_question' => 'array'];


    public function getQuestionsToGo(): array
    {
        return $this->questions_to_go ?? [];
    }

    public function getCurrentQuestion(): array
    {
        return $this->current_question ?? [];
    }

    public function getChatId(): string
    {
        return $this->chat_id ?? '';
    }

}
