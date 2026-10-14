<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
    }

    private function category(): Category
    {
        return Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
            'is_active' => true,
        ]);
    }

    public function test_author_can_upload_book_and_it_enters_moderation_queue(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $category = $this->category();

        $response = $this->actingAs($author)->post(route('author.books.store'), [
            'title' => 'Buku Uji Fase Tiga',
            'description' => 'Deskripsi buku uji.',
            'author_name' => $author->name,
            'category_id' => $category->id,
            'language' => 'id',
            'price' => 50000,
            'cover' => UploadedFile::fake()->image('cover.jpg'),
            'file' => UploadedFile::fake()->create('buku.epub', 100, 'application/epub+zip'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $book = Book::first();
        $this->assertNotNull($book);
        $this->assertSame(BookStatus::PENDING_REVIEW, $book->status);
        $this->assertSame($author->id, $book->publisher_id);
        $this->assertNotNull($book->cover_path);
        $this->assertNotNull($book->s3_key);
        Storage::disk('r2')->assertExists($book->cover_path);
        Storage::disk('r2')->assertExists($book->s3_key);
    }

    public function test_reader_cannot_access_book_management(): void
    {
        $reader = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($reader)->get(route('author.books.index'))->assertForbidden();
    }

    public function test_published_book_appears_on_public_author_profile(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $category = $this->category();

        Book::create([
            'publisher_id' => $author->id,
            'title' => 'Karya Terbit',
            'slug' => 'karya-terbit',
            'description' => 'x',
            'author_name' => $author->name,
            'category_id' => $category->id,
            'language' => 'id',
            'price' => 10000,
            'status' => BookStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        Book::create([
            'publisher_id' => $author->id,
            'title' => 'Masih Draft',
            'slug' => 'masih-draft',
            'description' => 'x',
            'author_name' => $author->name,
            'category_id' => $category->id,
            'language' => 'id',
            'price' => 10000,
            'status' => BookStatus::DRAFT,
        ]);

        $this->get(route('public.author', $author))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/AuthorProfile')
                ->has('books', 1)
                ->where('books.0.title', 'Karya Terbit'));
    }

    public function test_public_author_profile_404_for_non_author(): void
    {
        $reader = User::factory()->create(['role' => UserRole::USER]);

        $this->get(route('public.author', $reader))->assertNotFound();
    }
}
