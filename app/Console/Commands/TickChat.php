<?php

namespace App\Console\Commands;

use App\Data\Contracts\ITelegramRequest;
use App\Data\MessageTextData;
use App\Data\QuestionPhotoData;
use App\Data\QuestionTextData;
use App\Data\CallbackAnswerData;
use App\Data\RequestEditData;
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
        $updateResponse = $botService->getUpdates(
            new RequestUpdateData('callback_query')
        );

        if (!$updateResponse instanceof CallbackUpdateData) {
            return;
        }

        $callbackData = $updateResponse->getCallbackData();

        switch ($callbackData) {

            case ('start'):

                $questions = Question::with('images')->get()->shuffle();
                foreach ($questions as $question) {
                    $botService->sendMessage($this->getQuestionData($question));
                }
                break;

                case ('finish'):

                    $rightAnswersCount = Answer::where('user_name', $updateResponse->getUserName())
                        ->where('is_correct', true)
                        ->get()
                        ->count();

                    $questionsCount = Question::all()->count();

                    Score::create([
                        'user_name' => $updateResponse->getUserName(),
                        'score' => $rightAnswersCount,
                        'max_score' => $questionsCount,
                    ]);

                    $botService->sendMessage(MessageTextData::from([
                        'text' => sprintf('Your score is: %s/%s', $rightAnswersCount, $questionsCount),
                    ]));

                    Answer::where('user_name', $updateResponse->getUserName())->delete();
                    break;

                case ('top'):

                    $scores = Score::orderBy('score', 'desc')->take(3)->get();
                    $texts = [];

                    foreach ($scores as $score) {
                        $text = sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
                        $texts[] = $text;
                    }

                    $botService->sendMessage(MessageTextData::from([
                        'text' => implode(",", $texts),
                    ]));
                    break;

                default:

                    $question = Question::where('body', $updateResponse->getMessageText())
                        ->get()
                        ->first();

                    $question->answers()
                        ->create([
                            'user_name' => $updateResponse->getUserName(),
                            'user_id' => $updateResponse->getUserId(),
                            'is_correct' => (bool) $updateResponse->getCallbackData(),
                        ]);

                    $botService->answerCallbackQuery(CallbackAnswerData::from([
                        'callbackId' => $updateResponse->getCallbackQueryId(),
                        'isCorrect' => (bool) $updateResponse->getCallbackData(),
                        'userName' => $updateResponse->getUserName(),
                    ]));

                    $botService->editMessageText(RequestEditData::from([
                        'messageId' => $updateResponse->getMessageId(),
                        'messageText' => $updateResponse->getMessageText(),
                    ]));

                    break;

            }
            file_put_contents(config('telegramBot.cursorPath'), $updateResponse->getUpdateId() + 1);

    }

    public function getQuestionData(Question $question): ITelegramRequest
    {

        return $question->images->isEmpty() ?
            $data = QuestionTextData::from([
                'answers' => collect(json_decode($question->answers, true)),
                'text' => $question->body,
                ]) :
            $data =  QuestionPhotoData::from([
                'answers' => collect($question->answers),
                'text' => $question->body,
                'photo' => $question->images()->first()->name,
                ]);

    }

}
