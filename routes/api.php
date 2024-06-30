<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::middleware('api.token')->group(function () {
    Route::any('/v1', [ApiController::class, 'handleRequest']);
});
