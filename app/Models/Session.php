<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['questions_to_go' => 'array'];


    public function getQuestions()
    {
        //это пиздец
        return json_decode($this->questions_to_go);
    }

}
