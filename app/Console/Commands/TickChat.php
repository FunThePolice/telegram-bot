<?php

namespace App\Console\Commands;

use App\Data\Contracts\ITelegramRequest;
use App\Data\Contracts\ITelegramResponse;
use App\Data\Requests\CallbackAnswerData;
use App\Data\Requests\MessageTextData;
use App\Data\Requests\QuestionPhotoData;
use App\Data\Requests\QuestionTextData;
use App\Data\Requests\RequestEditData;
use App\Data\Requests\RequestUpdateData;
use App\Data\Responses\CallbackUpdateData;
use App\Data\Responses\MessageUpdateData;
use App\Data\Responses\PollAnswerData;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Score;
use App\Models\Session;
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
    protected $description = 'Sends request for updates from telegram, processes given result based on result type';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $botService = app(TelegramBotService::class);
        $updateResponse = $botService->getUpdates(
            new RequestUpdateData(['callback_query','message','poll_answer'])
        );

        file_put_contents(config('telegramBot.cursorPath'), $updateResponse->getUpdateId() + 1);
        switch ($updateResponse) {

            case ($updateResponse instanceof CallbackUpdateData):
                $this->processCallback($updateResponse, $botService);
                break;

                case ($updateResponse instanceof MessageUpdateData):
                    $this->processCommand($updateResponse, $botService);
                    break;

                        case ($updateResponse instanceof PollAnswerData):
                            $this->saveAnswer($updateResponse);
                            break;
        }
    }

    /** @var CallbackUpdateData $updateResponse */
    protected function processCallback(ITelegramResponse $updateResponse, TelegramBotService $botService): void
    {

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
    }

    /** @var MessageUpdateData $updateResponse */
    protected function processCommand(ITelegramResponse $updateResponse, TelegramBotService $botService): void
    {
        $command = $updateResponse->getText();

        switch ($command) {

            case ('/start' . config('telegramBot.bot_name')):

                Session::create([
                    'chat_id' => $updateResponse->getChatId(),
                    'questions_to_go' => json_encode(Question::all()),
                ]);
                break;

            case ('/top' . config('telegramBot.bot_name')):

                $scores = Score::orderBy('score', 'desc')->take(3)->get();

                if ($scores->isEmpty()) {
                    $botService->sendMessage(MessageTextData::from([
                        'chat_id' => $updateResponse->getChatId(),
                        'text' => 'No scores available.',
                    ]));
                    return;
                }

                $texts = $scores->map(function ($score) {
                    return sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
                })->toArray();

                $botService->sendMessage(MessageTextData::from([
                    'chatId' => $updateResponse->getChatId(),
                    'text' => implode("\n", $texts),
                ]));
                break;

        }
    }

    /** @var PollAnswerData $updateResponse */
    protected function saveAnswer(ITelegramResponse $updateResponse): void
    {
        $session = Session::where('poll_id', $updateResponse->getPollId())->get()->first();
        $currentQuestion = json_decode($session->current_question, true);
        $isCorrect = $currentQuestion['correctId'] === $updateResponse->getOptionId();

        Answer::create([
            'chat_id' => $session->chat_id,
            'user_name' => $updateResponse->getUserName(),
            'user_id' => $updateResponse->getUserId(),
            'is_correct' => $isCorrect,
        ]);
    }

    protected function getQuestionData(Question $question): ITelegramRequest
    {
        return $question->images->isEmpty() ?
            QuestionTextData::from([
                'answers' => collect(json_decode($question->answers, true)),
                'text' => $question->body,
            ]) :
            QuestionPhotoData::from([
                'answers' => collect($question->answers),
                'text' => $question->body,
                'photo' => $question->images()->first()->name,
            ]);
    }

}
