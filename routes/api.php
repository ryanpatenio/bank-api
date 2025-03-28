<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\ApiRequestController;
use Illuminate\Support\Facades\Route;


Route::middleware('validateApiKey')->group(function(){
    Route::post('/process-credit',[ApiRequestController::class,'credit']);
    Route::post('/process-debit',[ApiRequestController::class,'debit']);

});

Route::get('/test',[ApiKeyController::class,'test']);

