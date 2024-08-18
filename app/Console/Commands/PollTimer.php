<?php

namespace App\Console\Commands;

use App\Exceptions\QuizHandlerFactoryConditionsAreNotMet;
use App\Exceptions\QuizSessionDataIsCorrupted;
use App\Factories\QuizHandlerFactory\QuizHandlerFactory;
use App\Models\Session;
use App\Services\Poll;
use App\Services\SessionService;
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
    protected $description = 'Manages poll timer and Quiz Session in DB, processes quiz in different channels based on
    session state of each channel';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $botService): void
    {
        $sessions = Session::all();

        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($sessions as $session) {

            try {
                (new QuizHandlerFactory())->createHandler(new SessionService($session))->handle($botService);
            } catch (QuizHandlerFactoryConditionsAreNotMet $e) {
                return;
            }

        }
    }

}
