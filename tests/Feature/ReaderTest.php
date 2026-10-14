<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Highlight;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Services\ReaderAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReaderTest extends TestCase
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
            'slug' => 'fiksi-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function makeBook(User $owner, float $price, BookStatus $status, string $slug): Book
    {
        $book = Book::create([
            'publisher_id' => $owner->id,
            'title' => 'Buku ' . $slug,
            'slug' => $slug,
            'description' => 'Deskripsi',
            'author_name' => $owner->name,
            'category_id' => $this->category()->id,
            'language' => 'id',
            'price' => $price,
            'status' => $status,
            'published_at' => $status === BookStatus::PUBLISHED ? now() : null,
            's3_key' => 'books/' . $slug . '.epub',
        ]);

        Storage::disk('r2')->put($book->s3_key, 'dummy-epub-content');

        return $book;
    }

    /* ---------------- ReaderAccessService ---------------- */

    public function test_free_published_book_is_readable_by_any_user(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'gratis');

        $this->assertTrue(app(ReaderAccessService::class)->canRead($reader, $book));
    }

    public function test_paid_published_book_is_sample_for_non_buyer(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, BookStatus::PUBLISHED, 'berbayar');

        $service = app(ReaderAccessService::class);
        $this->assertFalse($service->canRead($reader, $book));
        $this->assertTrue($service->canSample($reader, $book));
    }

    public function test_owner_can_read_own_unpublished_book(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->makeBook($owner, 50000, BookStatus::DRAFT, 'draft-penulis');

        $this->assertTrue(app(ReaderAccessService::class)->canRead($owner, $book));
    }

    public function test_unpublished_book_denied_for_other_user(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, BookStatus::DRAFT, 'draft-orang');

        $this->assertNull(app(ReaderAccessService::class)->resolve($reader, $book)['mode']);
    }

    /* ---------------- Reader endpoints ---------------- */

    public function test_guest_is_redirected_to_login(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'guest-redirect');

        $this->get(route('reader.show', $book))->assertRedirect(route('login'));
    }

    public function test_reader_page_renders_for_free_book(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'halaman-baca');

        $this->actingAs($reader)
            ->get(route('reader.show', $book))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Reader/Show')
                ->where('mode', 'full')
                ->where('book.type', 'epub'));
    }

    public function test_paid_book_reader_page_is_sample_mode(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, BookStatus::PUBLISHED, 'halaman-sampel');

        $this->actingAs($reader)
            ->get(route('reader.show', $book))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Reader/Show')
                ->where('mode', 'sample'));
    }

    public function test_denied_user_is_redirected_with_error(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, BookStatus::DRAFT, 'ditolak');

        $this->actingAs($reader)
            ->get(route('reader.show', $book))
            ->assertRedirect(route('catalog.show', $book->slug))
            ->assertSessionHas('error');
    }

    public function test_progress_is_saved(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'progres');

        $this->actingAs($reader)
            ->postJson(route('reader.progress', $book), ['location' => 'epubcfi(/6/4)', 'percent' => 42])
            ->assertOk();

        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $reader->id,
            'book_id' => $book->id,
            'percent' => 42,
        ]);

        // Update (bukan duplikat)
        $this->actingAs($reader)
            ->postJson(route('reader.progress', $book), ['location' => 'epubcfi(/6/8)', 'percent' => 80])
            ->assertOk();

        $this->assertSame(1, ReadingProgress::where('user_id', $reader->id)->count());
        $this->assertSame(80, ReadingProgress::first()->percent);
    }

    public function test_bookmark_create_and_delete(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'bookmark');

        $res = $this->actingAs($reader)
            ->postJson(route('reader.bookmarks.store', $book), ['location' => 'epubcfi(/6/10)', 'label' => 'Bab 2'])
            ->assertOk();

        $id = $res->json('bookmark.id');
        $this->assertDatabaseHas('bookmarks', ['id' => $id, 'label' => 'Bab 2']);

        $this->actingAs($reader)
            ->deleteJson(route('reader.bookmarks.destroy', ['book' => $book->id, 'bookmark' => $id]))
            ->assertOk();

        $this->assertDatabaseMissing('bookmarks', ['id' => $id]);
    }

    public function test_highlight_create_and_delete(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'highlight');

        $res = $this->actingAs($reader)
            ->postJson(route('reader.highlights.store', $book), [
                'location' => 'epubcfi(/6/12)',
                'text' => 'Kutipan penting',
                'color' => 'green',
            ])
            ->assertOk();

        $id = $res->json('highlight.id');
        $this->assertDatabaseHas('highlights', ['id' => $id, 'text' => 'Kutipan penting', 'color' => 'green']);

        $this->actingAs($reader)
            ->deleteJson(route('reader.highlights.destroy', ['book' => $book->id, 'highlight' => $id]))
            ->assertOk();

        $this->assertDatabaseMissing('highlights', ['id' => $id]);
    }

    public function test_user_cannot_delete_another_users_bookmark(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $other = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 0, BookStatus::PUBLISHED, 'izin-bookmark');

        $bookmark = Bookmark::create([
            'user_id' => $other->id,
            'book_id' => $book->id,
            'location' => 'epubcfi(/6/14)',
        ]);

        $this->actingAs($reader)
            ->deleteJson(route('reader.bookmarks.destroy', ['book' => $book->id, 'bookmark' => $bookmark->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark->id]);
    }

    public function test_preferences_are_persisted(): void
    {
        $reader = User::factory()->create(['role' => UserRole::USER]);

        $this->actingAs($reader)
            ->patchJson(route('reader.preferences'), [
                'theme' => 'dark',
                'font_size' => 20,
                'font_family' => 'sans-serif',
                'line_height' => 1.8,
                'margin' => 'wide',
            ])
            ->assertOk();

        $this->assertDatabaseHas('reading_preferences', [
            'user_id' => $reader->id,
            'theme' => 'dark',
            'font_size' => 20,
        ]);
    }
}
