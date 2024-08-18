<?php

namespace App\Services;

use App\Data\Requests\MessageTextData;
use App\Models\Answer;
use App\Models\Score;
use Illuminate\Support\Collection;

class ScoreService
{
    const NO_SCORES_AVAILABLE_TEXT = 'No scores available.';

    const TOP_NUMBER = 3;

    protected Collection $scores;

    public function saveScores(int $chatId): void
    {
        $this->getPersonalizedScore($chatId)->map(function ($score) {
            Score::create($score);
        });
    }
    public function getChatTopScoresRequest(string $chatId): MessageTextData
    {
        $this->getScores();

        return $this->isEmpty() ?
            MessageTextData::from([
                'chatId' => $chatId,
                'text' => static::NO_SCORES_AVAILABLE_TEXT,
            ]) :
            MessageTextData::from([
                'chatId' => $chatId,
                'text' => $this->getPersonalizedScoreTexts(),
            ]);
    }

    protected function getPersonalizedScoreTexts(): string
    {
        return $this->scores->map(function ($score) {
            return sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
        });
    }

    public function getPersonalizedScore(int $chatId)
    {
        $answers = Answer::where('chat_id', $chatId)->get();
        return $answers->groupBy('user_name')->map(function ($userAnswers, $userName) {
            $correctCount = $userAnswers->where('is_correct', true)->count();
            $totalCount = $userAnswers->count();

            return [
                    'user_name' => $userName,
                    'score' => $correctCount,
                    'max_score' => $totalCount
            ];
        });
    }
    protected function getScores(): ScoreService
    {
        $this->scores = Score::orderBy('score', 'desc')->take(static::TOP_NUMBER)->get();
        return $this;
    }

    protected function isEmpty(): bool
    {
        return $this->scores->isEmpty();
    }

}
