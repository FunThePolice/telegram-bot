<?php

use App\Http\Controllers\BotController;
use App\Http\Controllers\QuestionController;
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

Route::get('/', [QuestionController::class, 'index'])->name('index');
Route::post('/question', [QuestionController::class, 'create'])->name('store');
Route::put('/question/{question}', [QuestionController::class, 'update'])->name('update');
Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('delete');
Route::get('/message', [BotController::class, 'sendMessage'])->name('message');
Route::post('/message', [BotController::class, 'createQuestion'])->name('question');
Route::get('/message/update', [BotController::class, 'getUpdate'])->name('update');
Route::post('/message', [BotController::class, 'sendQuestion'])->name('control.message');
