<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsWebController;

Route::get('/', [NewsWebController::class, 'index'])->name('news.index');
Route::get('/article/{article}', [NewsWebController::class, 'show'])->name('news.show');