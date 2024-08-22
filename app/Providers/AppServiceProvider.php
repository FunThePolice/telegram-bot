<?php

namespace App\Providers;

use App\Models\Session;
use App\Repositories\AnswerRepository;
use App\Repositories\QuestionRepository;
use App\Services\CommandService;
use App\Services\ScoreService;
use App\Services\SessionService;
use App\Services\TelegramBotService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\TelegramBotHandler;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TelegramBotService::class, function () {
            return new TelegramBotService(
                new Client(['base_uri' => 'https://api.telegram.org/bot' . config('telegramBot.token') . '/'])
            );
        });

        $this->app->bind(SessionService::class, function ($app,$params) {
            return new SessionService(
                $params['session'],
                $app->make(QuestionRepository::class),
                $app->make(TelegramBotService::class),
                $app->make(ScoreService::class),
                $app->make(AnswerRepository::class)
            );
        });

        $this->app->bind(CommandService::class, function ($app,$params) {
            return new CommandService(
                $params['commandData'],
                $app->make(TelegramBotService::class),
                $app->make(QuestionRepository::class),
                $app->make(ScoreService::class),
            );
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
