<?php

namespace App\Console\Commands;

use App\Concerns\GetsCorrectAnswerId;
use App\Models\Session;
use App\Services\QuizService;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class PollTimer extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll-timer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages poll timer, defines app behavior based on state of related entities(sessions)';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $botService, QuizService $quizService)
    {
        $sessions = Session::all();

        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($sessions as $session) {
            $quizService->processQuiz($session, $botService);
        }
    }

}
