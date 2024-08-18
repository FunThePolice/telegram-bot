<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Session;
use Illuminate\Support\Collection;

class Poll
{
    protected Session $session;
    protected ?Question $currentQuestion;
    protected ?Collection $questionsToGo;

    public function __construct(Session $session)
    {
       $this->session = $session;
    }

    public function getCurrentQuestion(): Question
    {
        if (!$this->currentQuestion) {
            $this->currentQuestion = Question::find($this->session->current_question);
        }

        return $this->currentQuestion;
    }

    public function getQuestionsToGo(): Collection
    {
        if (!$this->questionsToGo) {
            $this->questionsToGo = Question::whereIn('id', $this->session->questions_to_go)->get();
        }

        return $this->questionsToGo;
    }

    public function forgetQuestion(Question $question): void
    {
        $this->questionsToGo = $this->questionsToGo->filter(function (Question $questionToGo) use($question) {
            return $questionToGo->id !== $question->id;
        });
    }

    public function isTimeToAnswerExpired(): bool
    {
        return $this->session->updated_at
            ->addSeconds(config('telegramBot.time_to_answer'))
            ->isPast();
    }

    public function getSession(): Session
    {
        return $this->session;
    }
}
