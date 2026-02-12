<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: "/api/books",
        summary: "Lister les livres (pagination)",
        tags: ["Books"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Succès",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "1984"),
                                    new OA\Property(property: "author", type: "string", example: "George Orwell"),
                                    new OA\Property(property: "isbn", type: "string", example: "9781234567897"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "links",
                            type: "object",
                            properties: [
                                new OA\Property(property: "first", type: "string", example: "http://localhost:8000/api/books?page=1"),
                                new OA\Property(property: "last", type: "string", example: "http://localhost:8000/api/books?page=5"),
                                new OA\Property(property: "prev", type: "string", nullable: true, example: null),
                                new OA\Property(property: "next", type: "string", nullable: true, example: "http://localhost:8000/api/books?page=2"),
                            ]
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "from", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 5),
                                new OA\Property(property: "path", type: "string", example: "http://localhost:8000/api/books"),
                                new OA\Property(property: "per_page", type: "integer", example: 2),
                                new OA\Property(property: "to", type: "integer", example: 2),
                                new OA\Property(property: "total", type: "integer", example: 10),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        return BookResource::collection(Book::paginate(10));
    }

    #[OA\Post(
        path: "/api/books",
        summary: "Ajouter un livre",
        tags: ["Books"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "author", "summary", "isbn"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "1984"),
                    new OA\Property(property: "author", type: "string", example: "George Orwell"),
                    new OA\Property(property: "summary", type: "string", example: "Un futur dystopique..."),
                    new OA\Property(property: "isbn", type: "string", example: "1234567890123"),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Livre créé",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "1984"),
                                new OA\Property(property: "author", type: "string", example: "George Orwell"),
                                new OA\Property(property: "summary", type: "string", example: "Un futur dystopique..."),
                                new OA\Property(property: "isbn", type: "string", example: "1234567890123"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-02-12T12:00:00.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-02-12T12:00:00.000000Z"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Erreur de validation",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(property: "errors", type: "object", example: ["isbn" => ["The isbn field must be 13 characters."]])
                    ]
                )
            ),
        ],
    )]
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

    #[OA\Get(
        path: "/api/books/{id}",
        summary: "Détails d'un livre",
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Succès",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "1984"),
                                new OA\Property(property: "author", type: "string", example: "George Orwell"),
                                new OA\Property(property: "summary", type: "string", example: "Un futur dystopique..."),
                                new OA\Property(property: "isbn", type: "string", example: "1234567890123"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Livre non trouvé",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(properties: [new OA\Property(property: "message", type: "string", example: "Resource not found")])
            ),
        ],
    )]
    public function show($id)
    {
        $book = Cache::remember('book_' . $id, 3600, fn() => Book::findOrFail($id));
        return new BookResource($book);
    }

    #[OA\Put(
        path: "/api/books/{id}",
        summary: "Modifier un livre",
        security: [["bearerAuth" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Titre mis à jour"),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Modifié",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Titre mis à jour"),
                            new OA\Property(property: "author", type: "string", example: "Auteur"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Livre non trouvé",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(properties: [new OA\Property(property: "message", type: "string", example: "Livre introuvable")])
            ),
            new OA\Response(
                response: 422,
                description: "Erreur de validation",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(property: "errors", type: "object", example: ["isbn" => ["The isbn field must be 13 characters."]])
                    ]
                )
            ),
        ],
    )]
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'   => 'sometimes|string|min:3|max:255',
            'author'  => 'sometimes|string|min:3|max:100',
            'summary' => 'sometimes|string|min:10|max:500',
            'isbn'    => 'sometimes|string|size:13|unique:books,isbn,' . $book->id,
        ]);

        $book->update($validated);
        return new BookResource($book);
    }

    #[OA\Delete(
        path: "/api/books/{id}",
        summary: "Supprimer un livre",
        security: [["bearerAuth" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Supprimé avec succès",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Livre supprimé avec succès")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Livre non trouvé",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(properties: [new OA\Property(property: "message", type: "string", example: "Livre introuvable")])
            ),
        ],
    )]
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Livre supprimé avec succès']);
    }
}
