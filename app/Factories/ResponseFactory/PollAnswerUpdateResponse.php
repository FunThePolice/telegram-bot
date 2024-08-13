<?php

namespace App\Factories\ResponseFactory;

use App\Concerns\getsLastUpdateId;
use App\Contracts\IUpdateResponse;
use App\Data\Responses\PollAnswerData;
use Illuminate\Support\Collection;

class PollAnswerUpdateResponse implements IUpdateResponse
{
    use getsLastUpdateId;

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): PollAnswerData
    {
        $pollAnswer = $this->result['poll_answer'];

        return PollAnswerData::from([
            'pollId' => $pollAnswer['poll_id'] ?? 0,
            'updateId' => $this->result['update_id'] ?? $this->getLastUpdateId(),
            'userName' => $pollAnswer['user']['first_name'] ?? '',
            'userId' => $pollAnswer['user']['id'] ?? 0,
            'optionIds' => collect($pollAnswer['option_ids']) ?? [],
        ]);
    }

}
