<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // 1. Lister les livres (GET)
    public function index()
    {
        return BookResource::collection(Book::all());
    }

    // 2. Créer un livre (POST)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'author' => 'required|string|min:3|max:100',
            'summary' => 'required|string|min:10|max:500',
            'isbn' => 'required|string|size:13|unique:books,isbn',
        ]);

        $book = Book::create($validated);

        return new BookResource($book);
    }

    // 3. Afficher un livre (GET)
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    // 4. Modifier un livre (PUT/PATCH)
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|min:3|max:255',
            'author' => 'sometimes|string|min:3|max:100',
            'summary' => 'sometimes|string|min:10|max:500',
            'isbn' => 'sometimes|string|size:13|unique:books,isbn,' . $book->id,
        ]);

        $book->update($validated);

        return new BookResource($book);
    }

    // 5. Supprimer un livre (DELETE)
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->noContent();
    }
}
