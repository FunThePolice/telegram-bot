<?php

namespace App\Concerns;

use App\Data\Contracts\ITelegramResponse;
use App\Data\Responses\CallbackUpdateData;
use App\Data\Responses\MessageUpdateData;
use App\Data\Responses\PollAnswerData;
use App\Data\Responses\PollUpdateData;
use Illuminate\Support\Collection;
use RuntimeException;

trait HandlesResponse
{

    public function handleResponse(Collection $result): ITelegramResponse
    {
        $result = $result->first();
        $data = null;
        switch ($result) {

            case (isset($result['callback_query'])):
                $callback = $result['callback_query'];

                $data = CallbackUpdateData::from([
                    'updateId' => $result['update_id'],
                    'userName' => $callback['from']['first_name'],
                    'userId' => $callback['from']['id'],
                    'callbackData' => $callback['data'],
                    'messageId' => $callback['message']['message_id'],
                    'callbackQueryId' => $callback['id'],
                    'replyMarkup' => $callback['message']['reply_markup'],
                    'messageText' => $callback['message']['text'],
                ]);
                break;

                case (isset($result['message'])):
                    $message = $result['message'];

                    if (collect($message['entities'])->first()['type'] !== "bot_command") {
                        file_put_contents(config('telegramBot.cursorPath'), $message['update_id'] + 1);
                        break;
                    }

                    $data = MessageUpdateData::from([
                        'updateId' => $result['update_id'],
                        'chatId' => $message['chat']['id'],
                        'text' => $message['text'],
                        'type' => collect($message['entities'])->first()['type'],
                        'messageId' => $message['message_id'],
                        'senderName' => $message['from']['first_name'],
                        'senderId' => $message['from']['id'],
                        ]);
                    break;

                    case (isset($result['poll'])):
                        $poll = $result['poll'];

                        $data = PollUpdateData::from([
                            'updateId' => $result['update_id'],
                            'pollId' => $poll['id'],
                            'question' => $poll['question'],
                            'options' => collect($result['poll']['options']),
                            'correctId' => $result['poll']['correct_option_id'],
                            ]);
                        break;

                case (isset($result['poll_answer'])):
                    $pollAnswer = $result['poll_answer'];

                    $data = PollAnswerData::from([
                        'pollId' => $pollAnswer['poll_id'],
                        'updateId' => $result['update_id'],
                        'userName' => $pollAnswer['user']['first_name'],
                        'userId' => $pollAnswer['user']['id'],
                        'optionId' => collect($pollAnswer['option_ids'])->first(),
                    ]);
                    break;

                    default:
                        throw new RuntimeException('Unknown update type');
        }

        if (!$data) {
            throw new RuntimeException('Failed to handle response data');
        }

        return $data;
    }

}
