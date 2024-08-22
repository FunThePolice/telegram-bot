<?php

namespace App\Repositories;

use App\Data\Responses\PollAnswerData;
use App\Models\Answer;
use App\Models\Session;

class AnswerRepository
{
    public function create(PollAnswerData $pollAnswerData): void
    {
        $session = $this->getCurrentPollById($pollAnswerData->getPollId());

        if (!$session) {
            return;
        }

        Answer::create([
            'chat_id' => $session->getChatId(),
            'user_name' => $pollAnswerData->getUserName(),
            'user_id' => $pollAnswerData->getUserId(),
            'is_correct' => $this->isCorrectAnswer($pollAnswerData, $session),
        ]);
    }

    protected function isCorrectAnswer(PollAnswerData $pollAnswerData, Session $session): bool
    {
        return $session->getCurrentQuestion()->getQuestion()->getCorrectIds() === $pollAnswerData->getOptionIds()->toArray();
    }

    protected function getCurrentPollById(int $pollId): ?Session
    {
        return Session::where('poll_id', $pollId)->get()->first();
    }

    public function deleteByChatId(int $chatId): void
    {
        Answer::where('chat_id', $chatId)->delete();
    }

}
