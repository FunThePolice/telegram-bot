<?php

namespace App\Http\Controllers;

use App\Data\QuestionData;
use App\Exceptions\CorrectAnswerIsNotSet;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Services\FileService;
use App\Services\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuestionController extends Controller
{

    public function index(): View
    {
        $questions = Question::all();
        return view('message', compact('questions'));
    }

    /**
     * @throws CorrectAnswerIsNotSet
     * @throws ValidationException
     */
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
        $answers = $question->getAnswers()->toArray();
        $correctAnswers = $question->getCorrectAnswers()->toArray();
        return view('edit-question', compact('question', 'answers', 'correctAnswers'));
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
