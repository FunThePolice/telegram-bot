<?php

namespace App\Services;

use App\Data\Requests\MessageTextData;
use App\Models\Score;
use Illuminate\Support\Collection;

class ScoreService
{
    const NO_SCORES_AVAILABLE_TEXT = 'No scores available.';

    protected Collection $scores;

    public function isEmpty(): bool
    {
        return $this->scores->isEmpty();
    }

    public function getChatTopScoresRequest(string $chatId, int $topNumber = 3): MessageTextData
    {
        $this->getScores($topNumber);

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

    protected function getPersonalizedScoreTexts(): array
    {
        return $this->scores->map(function ($score) {
            return sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
        })->toArray();
    }

    protected function getScores(int $topNumber = 3): ScoreService
    {
        $this->scores = Score::orderBy('score', 'desc')->take($topNumber)->get();
        return $this;
    }

}
