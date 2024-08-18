<?php

namespace App\Factories\UpdateHandlerFactory;

use App\Contracts\ITelegramRequest;
use App\Contracts\IUpdateHandler;
use App\Data\Requests\CallbackAnswerData;
use App\Data\Requests\MessageTextData;
use App\Data\Requests\QuestionPhotoData;
use App\Data\Requests\QuestionTextData;
use App\Data\Requests\RequestEditData;
use App\Data\Responses\CallbackUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Score;
use App\Services\TelegramBotService;

class CallbackHandler implements IUpdateHandler
{

    protected CallbackUpdateData $callbackData;

    public function __construct(CallbackUpdateData $updateData)
    {
        $this->callbackData = $updateData;
    }

    public function handle(TelegramBotService $botService): void
    {
        $callbackData = $this->callbackData->getCallbackData();

        switch ($callbackData) {

            case ('start'):

                $this->commandStart($botService);
                break;

            case ('finish'):

                $this->commandFinish($botService);
                break;

            case ('top'):

                $this->commandTop($botService);
                break;

            default:

                $this->processAnswer($botService);
                break;

        }
    }

    protected function getQuestionData(Question $question): ITelegramRequest
    {
        return $question->images->isEmpty() ?
            QuestionTextData::from([
                'answers' => $question->getAnswers(),
                'text' => $question->getBody(),
            ]) :
            QuestionPhotoData::from([
                'answers' => $question->getAnswers(),
                'text' => $question->getBody(),
                'photo' => $question->images()->first()->name,
            ]);
    }

    protected function commandStart(TelegramBotService $botService): void
    {
        $questions = Question::with('images')->get()->shuffle();
        foreach ($questions as $question) {

            try {
                $botService->sendMessage($this->getQuestionData($question));
            } catch (InvalidResponseTypeException|UpdateIsEmptyException $e) {
                return;
            }

        }
    }

    protected function commandFinish(TelegramBotService $botService): void
    {
        $rightAnswersCount = Answer::where('user_name', $this->callbackData->getUserName())
            ->where('is_correct', true)
            ->get()
            ->count();

        $questionsCount = Question::all()->count();

        Score::create([
            'user_name' => $this->callbackData->getUserName(),
            'score' => $rightAnswersCount,
            'max_score' => $questionsCount,
        ]);

        try {
            $botService->sendMessage(MessageTextData::from([
                'text' => sprintf('Your score is: %s/%s', $rightAnswersCount, $questionsCount),
            ]));
        } catch (InvalidResponseTypeException|UpdateIsEmptyException $e) {
            return;
        }

        Answer::where('user_name', $this->callbackData->getUserName())->delete();
    }

    protected function commandTop(TelegramBotService $botService): void
    {
        $scores = Score::orderBy('score', 'desc')->take(3)->get();
        $texts = [];

        foreach ($scores as $score) {
            $text = sprintf('%s: %s/%s', $score->user_name, $score->score, $score->max_score);
            $texts[] = $text;
        }

        try {
            $botService->sendMessage(MessageTextData::from([
                'text' => implode(",", $texts),
            ]));
        } catch (InvalidResponseTypeException|UpdateIsEmptyException $e) {
            return;
        }

    }

    protected function processAnswer(TelegramBotService $botService): void
    {
        $question = Question::where('body', $this->callbackData->getMessageText())
            ->get()
            ->first();

        $question->answers()
            ->create([
                'user_name' => $this->callbackData->getUserName(),
                'user_id' => $this->callbackData->getUserId(),
                'is_correct' => (bool) $this->callbackData->getCallbackData(),
            ]);

        try {
            $botService->answerCallbackQuery(CallbackAnswerData::from([
                'callbackId' => $this->callbackData->getCallbackQueryId(),
                'isCorrect' => (bool) $this->callbackData->getCallbackData(),
                'userName' => $this->callbackData->getUserName(),
            ]));
        } catch (InvalidResponseTypeException|UpdateIsEmptyException $e) {
            return;
        }

        try {
            $botService->editMessageText(RequestEditData::from([
                'messageId' => $this->callbackData->getMessageId(),
                'messageText' => $this->callbackData->getMessageText(),
            ]));
        } catch (InvalidResponseTypeException|UpdateIsEmptyException $e) {
            return;
        }

    }

}
