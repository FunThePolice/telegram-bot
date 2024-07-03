<?php

use App\Http\Controllers\BotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('message');
});
Route::get('/message', [BotController::class, 'sendMessage'])->name('message');
Route::post('/message', [BotController::class, 'sendQuestion'])->name('question');
Route::get('/message/updates', [BotController::class, 'getUpdates'])->name('updates');
Route::get('/message/callbacks', [BotController::class, 'getCallback'])->name('callbacks');
