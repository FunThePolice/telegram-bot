<?php

namespace App\Factories\QuizHandlerFactory;

use App\Contracts\IQuizHandler;
use App\Data\Requests\MessageTextData;
use App\Models\Answer;
use App\Models\Score;
use App\Models\Session as SessionModel;
use App\Services\TelegramBotService;

class FinishQuizHandler implements IQuizHandler
{

    protected SessionModel $session;

    public function __construct(SessionModel $session)
    {
        $this->session = $session;
    }

    public function handle(TelegramBotService $botService)
    {
        $answers = Answer::where('chat_id', $this->session->chat_id)->get();

        if (is_null($answers)) {
            $botService->sendMessage(MessageTextData::from([
                'chatId' => $this->session->chat_id,
                'text' => 'No answers in this quiz'
            ]));
            return;
        }

        $results = $answers->groupBy('user_name')->map(function ($userAnswers, $userName) {
            $correctCount = $userAnswers->where('is_correct', true)->count();
            $totalCount = $userAnswers->count();
            return [
                'resultText' => sprintf('%s:%s/%s',$userName, $correctCount, $totalCount),
                'result' => [
                    'user_name' => $userName,
                    'score' => $correctCount,
                    'max_score' => $totalCount
                ],
            ];
        });

        $resultText = $results->pluck('resultText')->all();
        $result = $results->pluck('result')->all();

        Score::upsert($result,['user_name']);

        $botService->sendMessage(MessageTextData::from([
            'chatId' => $this->session->chat_id,
            'text' => implode("\n", $resultText),
        ]));

        Answer::where('chat_id', $this->session->chat_id)->delete();
        $this->session->delete();
    }

}
