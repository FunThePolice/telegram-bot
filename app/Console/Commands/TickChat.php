<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class TickChat extends Command
{

    const PATH_TO_CURSOR = __DIR__ . '/../../../resources/bot_update_id_cursor';

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
    protected $description = 'Check for unread messages, if has callback_query handles the answer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botService = app(TelegramBotService::class);
        $update = collect($botService->getCallback()['result'][0]);

        if ($update->has('callback_query')) {
            $answer = $botService->prepareAnswer($update);

            Question::where('message_id', $update['callback_query']['message']['message_id'])
                ->get()
                ->first()
                ->answers()
                ->create([
                    'user_name' => $answer['name'],
                    'user_id' => $answer['user_id'],
                    'is_correct' => $answer['is_correct']
                ]);

            file_put_contents(self::PATH_TO_CURSOR, $update['update_id'] + 1);
            $botService->answerCallbackQuery($answer);
            $botService->editMessage([
                'message_id' => $update['callback_query']['message']['message_id'],
                'text' => $update['callback_query']['message']['text']."\n". $answer['name']. ': '.$answer['message'],
                'reply_markup' => json_encode($update['callback_query']['message']['reply_markup']),
            ]);
        }

    }
}
