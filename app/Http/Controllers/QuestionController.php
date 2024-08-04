<?php

namespace App\Http\Controllers;

use App\Concerns\FiltersAnswers;
use App\Data\QuestionData;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Services\FileService;
use App\Services\QuestionService;
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

    public function create(QuestionRequest $request, QuestionService $questionServices): RedirectResponse
    {
        $question = $questionServices->createQuestion(QuestionData::from($request->validated()));

        if ($request->hasFile('image')) {
            FileService::storeRelatedImage($request->file('image'), $question);
        }

        return redirect()->route('index');
    }

    public function edit(Question $question): View
    {
        $answers = json_decode($question->answers, true);
        return view('edit-question', compact('question', 'answers'));
    }

    public function update(QuestionRequest $request, Question $question, QuestionService $questionService): RedirectResponse
    {
        $questionService->updateQuestion($question, QuestionData::from($request->validated()));

        if ($request->hasFile('image')) {
            FileService::updateRelatedImage($request->file('image'), $question);
        }

        return redirect()->route('index');
    }

    public function delete(Question $question): RedirectResponse
    {
        $question->delete();
        FileService::deleteRelatedImages($question);

        return redirect()->route('index');
    }

}
