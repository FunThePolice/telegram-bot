<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Session;
use Illuminate\Support\Collection;

class Poll
{
    protected Session $question;
    protected ?Question $currentQuestion;
    protected ?Collection $questionsToGo;

    public function __construct(Session $question)
    {
       $this->question = $question;
    }

    public function getCurrentQuestion(): Question
    {
        if (!$this->currentQuestion) {
            $this->currentQuestion = Question::find($this->question->current_question);
        }

        return $this->currentQuestion;
    }

    public function getQuestionsToGo(): Collection
    {
        if (!$this->questionsToGo) {
            $this->questionsToGo = Question::whereIn('id', $this->question->questions_to_go)->get();
        }

        return $this->questionsToGo;
    }

    public function forgetQuestion(Question $question)
    {
        $this->questionsToGo = $this->questionsToGo->filter(function (Question $questionToGo) use($question) {
            return $questionToGo->id !== $question->id;
        });
    }

    public function isTimeToAnswerExpired(): bool
    {
        return $this->question->updated_at
            ->addSeconds(config('telegramBot.time_to_answer'))
            ->isPast();
    }

    public function getQuestion(): Session
    {
        return $this->question;
    }
}
