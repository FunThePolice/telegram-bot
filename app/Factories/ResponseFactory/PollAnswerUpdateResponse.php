<?php

namespace App\Factories\ResponseFactory;

use App\Contracts\IUpdateResponse;
use App\Data\Responses\PollAnswerData;
use Illuminate\Support\Collection;

class PollAnswerUpdateResponse implements IUpdateResponse
{

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): PollAnswerData
    {
        $pollAnswer = $this->result['poll_answer'];

        return PollAnswerData::from([
            'pollId' => $pollAnswer['poll_id'],
            'updateId' => $this->result['update_id'],
            'userName' => $pollAnswer['user']['first_name'],
            'userId' => $pollAnswer['user']['id'],
            'optionId' => collect($pollAnswer['option_ids'])->first(),
        ]);
    }

}
