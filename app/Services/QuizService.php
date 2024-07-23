<?php

namespace App\Services;

use App\Concerns\GetsCorrectAnswerId;
use App\Data\OptionData;
use App\Data\Requests\MessageTextData;
use App\Data\Requests\PollData;
use App\Data\Responses\PollUpdateData;
use App\Models\Answer;
use App\Models\Score;
use App\Models\Session as SessionModel;

class QuizService
{
    use GetsCorrectAnswerId;

    const TIME_TO_ANSWER = 30;

    public function processQuiz(SessionModel $session, TelegramBotService $botService): void
    {

        if (empty($session->current_question)) {
            $this->sendNextPoll($session, $botService);
            return;
        }

        if (empty(json_decode($session->questions_to_go))) {
            $this->finishQuiz($session, $botService);
            return;
        }

        if ($this->hasTimeToAnswerExpired($session)) {
            $this->sendNextPoll($session, $botService);
        }

    }

    protected function finishQuiz(SessionModel $session, TelegramBotService $botService): void
    {
        $answers = Answer::where('chat_id', $session->chat_id)->get();

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
            'chatId' => $session->chat_id,
            'text' => implode("\n", $resultText),
        ]));

        Answer::where('chat_id', $session->chat_id)->delete();
        $session->delete();

    }

    protected function sendNextPoll(SessionModel $session, TelegramBotService $botService): void
    {
        $sessionQuestions = collect(json_decode($session->questions_to_go));
        $question = $sessionQuestions->shuffle()->first();
        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));

        /*** @var PollUpdateData $poll */
        $poll = $botService->sendPoll(PollData::from([
            'chatId' => $session->chat_id,
            'text' => $question->body,
            'options' => OptionData::collect(json_decode($question->answers, true)),
            'openPeriod' => self::TIME_TO_ANSWER,
        ]));

        $session->update([
            'questions_to_go' => json_encode($updatedQuestionsToGo),
            'current_question' => json_encode([
                'question' => $question->body,
                'correctId' => $poll->getCorrectOptionId(),
            ]),
            'poll_id' => $poll->getPollId()
        ]);
    }

    protected function hasTimeToAnswerExpired(SessionModel $session): bool
    {
        return $session->updated_at->addSeconds(self::TIME_TO_ANSWER)->isPast();
    }

}
