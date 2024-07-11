<?php

namespace App\Console\Commands;

use App\Data\MessageTextData;
use App\Data\QuestionPhotoData;
use App\Data\QuestionTextData;
use App\Data\CallbackAnswerData;
use App\Data\TelegramRequestEditData;
use App\Data\RequestUpdateData;
use App\Data\CallbackUpdateData;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Score;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TickChat extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tick-chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for unread messages, if has callback_query handles the answer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botService = app(TelegramBotService::class);
        $update = $botService->sendRequest(
            new RequestUpdateData('callback_query')
        );

        if ($update instanceof CallbackUpdateData) {
            $callbackData = $update->getCallbackData();

            switch ($callbackData) {

                case ('start'):

                    $questions = Question::all()->shuffle();
                    foreach ($questions as $question) {

                        if ($question->images()->get()->isEmpty()) {
                            $data = QuestionTextData::from([
                                'answers' => collect(json_decode($question->answers, true)),
                                'text' => $question->body,
                            ]);
                        } else {
                            $data =  QuestionPhotoData::from([
                                'answers' => collect($question->answers),
                                'text' => $question->body,
                                'photo' => $question->images()->first()->name,
                            ]);
                        }

                        $botService->sendRequest($data);
                    }
                    break;

                case ('finish'):

                    $rightAnswersCount = Answer::where('user_name', $update->getUserName())
                        ->where('is_correct', true)
                        ->get()
                        ->count();

                    $questionsCount = Question::all()->count();

                    Score::create([
                        'user_name' => $update->getUserName(),
                        'score' => $rightAnswersCount,
                        'max_score' => $questionsCount,
                    ]);

                    $botService->sendRequest(MessageTextData::from([
                        'text' => sprintf('Your score is: %s/%s', $rightAnswersCount, $questionsCount),
                    ]));

                    Answer::where('user_name', $update->getUserName())->delete();
                    break;

                case ('top'):

                    $scores = Score::orderBy('score', 'desc')->take(3)->get();
                    $texts = [];

                    foreach ($scores as $score) {
                        $text = sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
                        $texts[] = $text;
                    }

                    $botService->sendRequest(MessageTextData::from([
                        'text' => implode(",", $texts),
                    ]));
                    break;

                default:

                    $question = Question::where('body', $update->getMessageText())
                        ->get()
                        ->first();

                    $question->answers()
                        ->create([
                            'user_name' => $update->getUserName(),
                            'user_id' => $update->getUserId(),
                            'is_correct' => (bool) $update->getCallbackData(),
                        ]);

                    $botService->sendRequest(CallbackAnswerData::from([
                        'callbackId' => $update->getCallbackQueryId(),
                        'isCorrect' => (bool) $update->getCallbackData(),
                        'userName' => $update->getUserName(),
                    ]));
                    break;

            }
            file_put_contents(config('telegramBot.cursorPath'), $update->getUpdateId() + 1);

        }

    }
}
