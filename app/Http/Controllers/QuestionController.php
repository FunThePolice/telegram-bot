<?php

namespace App\Http\Controllers;

use App\Concerns\FiltersAnswers;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
use FiltersAnswers;

    public function index(): View
    {
        $questions = Question::all();
        return view('message', compact('questions'));
    }

    public function create(QuestionRequest $request): RedirectResponse
    {
        $question = Question::create([
            'body' => $request->text,
            'answers' => $this->filterAnswers($request->answers),
            'correct_answer' => $this->getCorrectAnswer($request->answers)
        ]);

        if ($request->hasFile('image')) {
            FileService::storeRelatedImage($request->file('image'), $question);
        }

        return redirect()->route('index');
    }

    public function update(QuestionRequest $request, Question $question): RedirectResponse
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
