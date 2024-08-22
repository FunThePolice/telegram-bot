<?php

namespace App\Services;

use App\Data\Responses\PollUpdateData;
use App\Exceptions\InvalidResponseTypeException;
use App\Exceptions\UpdateIsEmptyException;
use App\Models\Question;
use App\Models\Session;
use App\Repositories\AnswerRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Support\Collection;

class SessionService
{

    protected Session $session;

    protected ?Question $currentQuestion = null;

    protected ?Collection $questionsToGo = null;

    protected QuestionRepository $questionService;
    protected TelegramBotService $botService;

    protected ScoreService $scoreService;

    protected AnswerRepository $answerService;


    public function __construct(
        Session            $session,
        QuestionRepository $questionService,
        TelegramBotService $botService,
        ScoreService       $scoreService,
        AnswerRepository   $answerService
    )
    {
        $this->session = $session;
        $this->questionService = $questionService;
        $this->botService = $botService;
        $this->scoreService = $scoreService;
        $this->answerService = $answerService;
    }

    public function getCurrentQuestion(): ?Question
    {
        if ($this->session->current_question) {
            return $this->questionService->findById($this->session->current_question);
        }

        return null;
    }

    public function getQuestionsToGo(): Collection
    {
        return $this->questionsToGo = Question::whereIn('id', $this->session->questions_to_go)->get();
    }

    protected function forgetQuestion(Question $question): void
    {
        $this->questionsToGo = $this->getQuestionsToGo()->filter(function (Question $questionToGo) use ($question) {
            return $questionToGo->id !== $question->id;
        });
    }

    public function isTimeToAnswerExpired(): bool
    {
        return $this->session->updated_at
            ->addSeconds(config('telegramBot.time_to_answer'))
            ->isPast();
    }

    protected function updateSession(int $pollId, int $questionId): void
    {
        $this->session->update([
            'questions_to_go' => $this->questionsToGo->pluck('id'),
            'current_question' => $questionId,
            'poll_id' => $pollId,
        ]);
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @throws InvalidResponseTypeException
     * @throws UpdateIsEmptyException
     */
    public function moveToNextQuestion(): void
    {
        $sessionQuestions = $this->getSession()->getQuestionsToGo();
        $questionToGo = $this->questionService->findById(
            $this->getRandomQuestion($sessionQuestions)
        );

        $this->forgetQuestion($questionToGo);

        /*** @var PollUpdateData $poll */
        $poll = $this->botService->sendPoll(
            (new PollService($this->getSession()))
                ->getPollData($questionToGo)
        );

        $this->updateSession($poll->getPollId(), $questionToGo->id);
    }


    /**
     * @throws InvalidResponseTypeException
     * @throws UpdateIsEmptyException
     */
    public function finishQuiz(): void
    {
        $chatId = $this->getSession()->getChatId();
        $this->scoreService->saveScores($chatId);

        $this->botService->sendMessage($this->scoreService->getChatTopScoresRequest($chatId));

        $this->answerService->deleteByChatId($chatId);
        $this->session->delete();
    }

    protected function getRandomQuestion(Collection $questions)
    {
        return $questions->shuffle()->first();
    }

}
