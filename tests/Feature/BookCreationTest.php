<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Scénario 1 : Tout est OK, le livre doit être créé (Code 201)
     */
    public function test_book_is_created_with_valid_data()
    {
        $user = User::factory()->create();

        $validData = [
            'title' => 'Harry Potter',
            'author' => 'J.K. Rowling',
            'summary' => 'Un jeune sorcier découvre son héritage magique...',
            'isbn' => '1234567890123'
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $validData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'Harry Potter',
            'isbn' => '1234567890123'
        ]);
    }

    /**
     * Scénario 2 : Données invalides (Titre trop court, ISBN faux/manquant...), le livre ne doit PAS être créé (Code 422)
     */
    public function test_book_is_not_created_with_invalid_data()
    {
        $user = User::factory()->create();

        $invalidData = [
            'title' => 'A',
            'author' => 'Moi',
            'summary' => 'Trop court',
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $invalidData);
        $response->assertStatus(422);

        $this->assertDatabaseMissing('books', [
            'title' => 'A'
        ]);
    }

    /**
     * Scénario 3 : Utilisateur non connecté, accès refusé (Code 401)
     */
    public function test_book_is_not_created_if_user_not_authenticated()
    {
        $bookData = [
            'title' => 'Livre Secret',
            'author' => 'Inconnu',
            'summary' => 'Ce livre ne devrait jamais être créé.',
            'isbn' => '9999999999999'
        ];

        $response = $this->postJson('/api/books', $bookData);
        $response->assertStatus(401);

        $this->assertDatabaseMissing('books', [
            'title' => 'Livre Secret'
        ]);
    }
}
