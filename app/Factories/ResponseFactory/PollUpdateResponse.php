<?php

namespace App\Factories\ResponseFactory;

use App\Contracts\IUpdateResponse;
use App\Data\Responses\PollUpdateData;
use Illuminate\Support\Collection;

class PollUpdateResponse implements IUpdateResponse
{

    protected Collection $result;

    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    public function create(): PollUpdateData
    {
        $poll = $this->result['poll'];

        return PollUpdateData::from([
            'updateId' => $this->result['update_id'] ?? null,
            'pollId' => $poll['id'],
            'question' => $poll['question'],
            'options' => collect($poll['options']),
            'correctOptionId' => $poll['correct_option_id'],
        ]);
    }

}
