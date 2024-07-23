<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
