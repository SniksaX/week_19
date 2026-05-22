<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_is_created_successfully_with_valid_data(): void
    {
        $user = User::factory()->create();

        $validData = [
            'title' => 'Le Seigneur des Anneaux',
            'author' => 'J.R.R. Tolkien',
            'summary' => 'Une quête épique pour détruire l\'Anneau unique.',
            'isbn' => '9780544003415'
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/books', $validData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'Le Seigneur des Anneaux',
            'isbn' => '9780544003415'
        ]);
    }

    public function test_book_is_not_created_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $invalidData = [
            'title' => 'Le',
            'author' => 'J.R.R. Tolkien',
            'summary' => 'Une quête épique pour détruire l\'Anneau unique.',
            'isbn' => '9780544003415'
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/books', $invalidData);

        $response->assertStatus(422);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_book_is_not_created_if_user_is_not_authenticated(): void
    {
        $bookData = [
            'title' => 'Le Seigneur des Anneaux',
            'author' => 'J.R.R. Tolkien',
            'summary' => 'Une quête épique pour détruire l\'Anneau unique.',
            'isbn' => '9780544003415'
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(401);

        $this->assertDatabaseCount('books', 0);
    }
}