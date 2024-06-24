<?php

namespace App\Providers;

use App\Services\TelegramBotService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\TelegramBotHandler;

class AppServiceProvider extends ServiceProvider
{

    const VICTORINA_BOT_API_TOKEN = '7475628763:AAG2TGF-BmgM4hyTYT29dcT9yBkO_HpOnB0';
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TelegramBotService::class, function (){
        return new TelegramBotService(
            new Client(['base_uri' => 'https://api.telegram.org/bot'.self::VICTORINA_BOT_API_TOKEN.'/'])
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
