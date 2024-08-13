<?php

namespace App\Factories\ResponseFactory;

use App\Concerns\getsLastUpdateId;
use App\Contracts\IUpdateResponse;
use App\Data\Responses\PollUpdateData;
use Illuminate\Support\Collection;

class PollUpdateResponse implements IUpdateResponse
{
    use getsLastUpdateId;

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): PollUpdateData
    {
        $poll = $this->result['poll'];

        return PollUpdateData::from([
            'updateId' => $this->result['update_id'] ?? $this->getLastUpdateId(),
            'pollId' => $poll['id'] ?? 0,
            'question' => $poll['question'] ?? '',
            'options' => collect($poll['options']) ?? [],
            'correctOptionIds' => collect($poll['correct_option_id'] ?? []),
        ]);
    }

}
