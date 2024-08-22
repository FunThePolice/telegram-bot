<?php

namespace App\Console\Commands;

use App\Exceptions\QuizHandlerFactoryConditionsAreNotMet;
use App\Factories\QuizHandlerFactory\QuizHandlerFactory;
use App\Models\Session;
use App\Repositories\QuestionRepository;
use App\Services\SessionService;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

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
    public function handle(): void
    {
        $sessions = Session::all();

        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($sessions as $session) {

            try {
                (new QuizHandlerFactory())
                    ->createHandler(
                        app()->makeWith(SessionService::class, ['session' => $session])
                    )->handle();
            } catch (QuizHandlerFactoryConditionsAreNotMet $e) {
                return;
            } catch (BindingResolutionException $e) {
            }

        }
    }

}
