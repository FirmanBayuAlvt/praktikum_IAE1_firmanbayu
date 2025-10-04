<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExternalApiController;


Route::get('/', [ExternalApiController::class, 'index']);


Route::get('/external', [ExternalApiController::class, 'index']);
