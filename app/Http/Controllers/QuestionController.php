<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index()
    {
        $questions = Question::all();
        return view('message', compact('questions'));
    }

    public function create(QuestionRequest $request)
    {
        $question = Question::create(QuestionResource::make($request)->resolve());

        if ($request->hasFile('image')) {
            FileService::storeRelatedImage($request->file('image'), $question);
        }

        return redirect()->route('index');
    }

    public function update(QuestionRequest $request, Question $question)
    {
        $question->update($request->except('image'));

        if ($request->hasFile('image')) {
            FileService::updateRelatedImage($request->file('image'), $question);
        }

        return redirect()->route('index');
    }

    public function delete(Question $question): RedirectResponse
    {
        $question->delete();
        $question->answers()->delete();
        FileService::deleteRelatedImages($question);

        return redirect()->route('index');
    }

}
