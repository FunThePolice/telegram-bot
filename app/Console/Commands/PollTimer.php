<?php

namespace App\Console\Commands;

use App\Concerns\GetsCorrectAnswerId;
use App\Data\Requests\MessageTextData;
use App\Data\Requests\PollData;
use App\Models\Answer;
use App\Models\Score;
use App\Models\Session;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class PollTimer extends Command
{
    use GetsCorrectAnswerId;

    const TIME_TO_ANSWER = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll-timer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages poll timer, defines app behavior based on state of related entities(sessions)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botService = app(TelegramBotService::class);
        $this->pollsTimer($botService);
    }

    protected function pollsTimer(TelegramBotService $botService): void
    {
        $sessions = Session::all();

        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($sessions as $session) {

            switch (true) {

                case (is_null($session->current_question)):
                    $this->sendNextPoll($session, $botService);
                    break;

                case (!$session->updated_at->addSeconds(self::TIME_TO_ANSWER)->isPast()):
                    break;

                case (empty($session->getQuestions())):
                    $this->finishQuiz($session, $botService);
                    break;

                default:
                    $this->sendNextPoll($session, $botService);
            }

        }
    }

    protected function finishQuiz(Session $session, TelegramBotService $botService): void
    {
        $answers = Answer::where('chat_id', $session->chat_id)->get();

        $results = $answers->groupBy('user_name')->map(function ($userAnswers) {
            return [
                'correct_count' => $userAnswers->where('is_correct', true)->count(),
                'total_count' => $userAnswers->count(),
            ];
        });

        $resultText = [];
        $result = [];
        foreach ($results as $userName => $userAnswers) {
            $resultText[] = sprintf('%s:%s/%s',$userName, $userAnswers['correct_count'], $userAnswers['total_count']);
            $result[] = [
                'user_name' => $userName,
                'score' => $userAnswers['correct_count'],
                'max_score' => $userAnswers['total_count'],
            ];
        }

        Score::upsert($result,['user_name']);

        $botService->sendMessage(MessageTextData::from([
            'chatId' => $session->chat_id,
            'text' => implode("\n", $resultText),
        ]));

        Answer::where('chat_id', $session->chat_id)->delete();
        $session->delete();

    }

    protected function sendNextPoll(Session $session, TelegramBotService $botService): void
    {
        $sessionQuestions = collect($session->getQuestions());
        $question = $sessionQuestions->shuffle()->first();

        $updatedQuestionsToGo = $sessionQuestions->forget($sessionQuestions->search($question));

        $poll = $botService->sendPoll(PollData::from([
            'chatId' => $session->chat_id,
            'text' => $question->body,
            'options' => json_decode($question->answers, true),
            'openPeriod' => self::TIME_TO_ANSWER,
        ]));

        $session->update([
            'questions_to_go' => json_encode($updatedQuestionsToGo),
            'current_question' => json_encode([
                'question' => $question->body,
                'correctId' => $this->getCorrectAnswerId(json_decode($question->answers, true)),
            ]),
            'poll_id' => $poll['poll']['id']
        ]);
    }

}
