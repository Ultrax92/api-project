<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;

Route::apiResource('books', BookController::class);
