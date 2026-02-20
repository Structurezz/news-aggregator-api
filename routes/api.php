<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {


    Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);


    Route::get('status', [StatusController::class, 'index'])->name('api.v1.status');

});