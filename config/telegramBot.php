<?php

return [
    'token' => env('VICTORINA_BOT_API_TOKEN'),
    'channel_id' => env('VICTORINA_BOT_CHANNEL_ID'),
    'cursorPath' => storage_path() . env('TELEGRAM_PATH_TO_CURSOR'),
    'images_path' => storage_path() . env('TELEGRAM_PATH_IMAGES'),
    'bot_name' => env('TELEGRAM_BOT_NAME'),
    'time_to_answer' => env('POLL_TIME_TO_ANSWER')
];
