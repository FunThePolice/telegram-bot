<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageWithPhotoResource;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class BotController extends Controller
{

    public function index()
    {

    }

    public function sendMessage(Request $request)
    {
        $botService = app(TelegramBotService::class);
        $botService->sendMessage($request->input('text'));
    }

    public function sendQuestion(Request $request)
    {
        $botService = app(TelegramBotService::class);

        if ($request->file('image')) {
            $question = $botService->sendMessageWithPhoto(MessageWithPhotoResource::make($request)->toArray($request));
        } else {
            $question = $botService->sendMessage(MessageResource::make($request)->toArray($request));
        }

        Question::create(array('message_id' => $question['result']['message_id']) + QuestionResource::make($request)->toArray($request));
    }

    public function getCallback()
    {
        $botService = app(TelegramBotService::class);
        return $botService->getCallback();
    }

    public function getUpdates()
    {
        $botService = app(TelegramBotService::class);
        return $botService->getUpdates();
    }

    public function answerCallback()
    {
        $botService = app(TelegramBotService::class);
        return $botService->answerCallback();
    }
}
