<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Session;
use Illuminate\Support\Collection;

class SessionService
{

    protected Session $session;

    protected ?Question $currentQuestion = null;

    protected ?Collection $questionsToGo = null;


    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getCurrentQuestion(): ?Question
    {
        return Question::find($this->session->current_question);
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
        $this->questionsToGo = $this->getQuestionsToGo()->filter(function (Question $questionToGo) use($question) {
            return $questionToGo->id !== $question->id;
        });
    }

    public function isTimeToAnswerExpired(): bool
    {
        return $this->session->updated_at
            ->addSeconds(config('telegramBot.time_to_answer'))
            ->isPast();
    }

    public function updateSession(int $pollId, int $questionId): void
    {
        $this->session->update([
            'questions_to_go' => $this->getQuestionsToGo()->pluck('id'),
            'current_question' => $questionId,
            'poll_id' => $pollId,
        ]);
    }

    public function getSession(): Session
    {
        return $this->session;
    }

}
