<?php

namespace App\Factories\ResponseFactory;

use App\Contracts\IUpdateResponse;
use App\Data\Responses\CallbackUpdateData;
use Illuminate\Support\Collection;

class CallbackUpdateResponse implements IUpdateResponse
{

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): CallbackUpdateData
    {
        $callback = $this->result['callback_query'];

        return CallbackUpdateData::from([
            'updateId' => $this->result['update_id'],
            'userName' => $callback['from']['first_name'],
            'userId' => $callback['from']['id'],
            'callbackData' => $callback['data'],
            'messageId' => $callback['message']['message_id'],
            'callbackQueryId' => $callback['id'],
            'replyMarkup' => $callback['message']['reply_markup'],
            'messageText' => $callback['message']['text'],
        ]);
    }

}
