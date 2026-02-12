<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Inscription d'un nouvel utilisateur",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur créé avec succès",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Utilisateur créé"),
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "john@example.com"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-02-12T10:00:00.000000Z"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-02-12T10:00:00.000000Z"),
                                new OA\Property(property: "id", type: "integer", example: 1)
                            ]
                        ),
                        new OA\Property(property: "token", type: "string", example: "2|TQ7kqea...")
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
                        new OA\Property(property: "message", type: "string", example: "The email has already been taken."),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            ),
        ],
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Utilisateur créé', 'user' => $user, 'token' => $token], 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Connexion utilisateur",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Connexion réussie"),
                        new OA\Property(property: "token", type: "string", example: "3|HMgNJYtn...")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Identifiants incorrects",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Identifiants incorrects")
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Connexion réussie', 'token' => $token]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnexion de l'utilisateur",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Déconnexion réussie",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Déconnexion réussie")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Non autorisé",
                headers: [
                    new OA\Header(header: "Content-Type", schema: new OA\Schema(type: "string", example: "application/json"))
                ],
                content: new OA\JsonContent(properties: [new OA\Property(property: "message", type: "string", example: "Unauthenticated.")])
            ),
        ],
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
