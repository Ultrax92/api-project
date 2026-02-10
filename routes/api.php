<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\UserController;

// --- ROUTES PUBLIQUES ---

Route::post('/register', [UserController::class, 'register']);

// Connexion avec limitation (10 requêtes par minute)
Route::middleware('throttle:10,1')->post('/login', [UserController::class, 'login']);

// Lecture des livres (Routes nommées)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// --- ROUTES PROTÉGÉES ---
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    // Gestion des livres (Routes nommées)
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::match(['put', 'patch'], '/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});
