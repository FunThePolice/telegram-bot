<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionWithPhotoResource;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class BotController extends Controller
{

    const TELEGRAM_CHANNEL_ID = '-4194487285';

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
            $botService->sendMessageWithPhoto(QuestionWithPhotoResource::make($request)->toArray($request));
        } else {
            $botService->sendMessage(QuestionResource::make($request)->toArray($request));
        }

    }

    public function setHook()
    {
        $botService = app(TelegramBotService::class);
        $botService->setHook();
    }
}
