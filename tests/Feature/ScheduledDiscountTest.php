<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\DiscountSource;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Services\PriceResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 6 — Diskon terjadwal per buku (PRD 4.7.1 / 4.4.4).
 */
class ScheduledDiscountTest extends TestCase
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

    private function book(User $owner, float $price, string $slug): Book
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
            'status' => BookStatus::PUBLISHED,
            'published_at' => now(),
            's3_key' => 'books/' . $slug . '.epub',
        ]);

        Storage::disk('r2')->put($book->s3_key, 'dummy-epub');

        return $book;
    }

    public function test_has_active_discount_respects_schedule_window(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'jadwal-window');

        // Dalam jendela.
        $book->update([
            'discount_price' => 75000,
            'discount_starts_at' => now()->subDay(),
            'discount_ends_at' => now()->addDay(),
        ]);
        $this->assertTrue($book->fresh()->hasActiveDiscount());

        // Belum mulai.
        $book->update(['discount_starts_at' => now()->addDay(), 'discount_ends_at' => now()->addDays(2)]);
        $this->assertFalse($book->fresh()->hasActiveDiscount());

        // Sudah berakhir.
        $book->update(['discount_starts_at' => now()->subDays(2), 'discount_ends_at' => now()->subDay()]);
        $this->assertFalse($book->fresh()->hasActiveDiscount());

        // Harga diskon tidak lebih murah dari harga jual.
        $book->update([
            'discount_price' => 100000,
            'discount_starts_at' => now()->subDay(),
            'discount_ends_at' => now()->addDay(),
        ]);
        $this->assertFalse($book->fresh()->hasActiveDiscount());
    }

    public function test_price_resolver_uses_discount_when_active_and_base_otherwise(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 120000, 'resolver-diskon');

        $book->update([
            'discount_price' => 90000,
            'discount_starts_at' => now()->subHour(),
            'discount_ends_at' => now()->addHour(),
        ]);

        $info = app(PriceResolver::class)->resolve($book->fresh());
        $this->assertSame(90000.0, $info['price']);
        $this->assertSame(DiscountSource::DISCOUNT, $info['source']);
        $this->assertSame(25, $info['percent']);

        // Setelah berakhir -> harga normal.
        $book->update(['discount_ends_at' => now()->subMinute()]);
        $info = app(PriceResolver::class)->resolve($book->fresh());
        $this->assertSame(120000.0, $info['price']);
        $this->assertSame(DiscountSource::NONE, $info['source']);
        $this->assertSame(0, $info['percent']);
    }

    public function test_author_can_schedule_discount_for_own_book(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'author-jadwal');

        $this->actingAs($author)->patch(route('author.books.discount', $book->id), [
            'discount_price' => 70000,
            'discount_starts_at' => now()->subMinute()->toDateTimeString(),
            'discount_ends_at' => now()->addDay()->toDateTimeString(),
        ])->assertSessionHasNoErrors();

        $book->refresh();
        $this->assertSame('70000.00', $book->discount_price);
        $this->assertTrue($book->hasActiveDiscount());
    }

    public function test_author_cannot_set_discount_at_or_above_sell_price(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'author-invalid');

        $this->actingAs($author)->patch(route('author.books.discount', $book->id), [
            'discount_price' => 100000,
            'discount_starts_at' => now()->toDateTimeString(),
            'discount_ends_at' => now()->addDay()->toDateTimeString(),
        ])->assertSessionHasErrors('discount_price');

        $this->assertNull($book->fresh()->discount_price);
    }

    public function test_clearing_discount_price_resets_schedule(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'author-hapus');

        $book->update([
            'discount_price' => 70000,
            'discount_starts_at' => now()->subDay(),
            'discount_ends_at' => now()->addDay(),
        ]);

        $this->actingAs($author)->patch(route('author.books.discount', $book->id), [
            'discount_price' => '',
            'discount_starts_at' => '',
            'discount_ends_at' => '',
        ])->assertSessionHasNoErrors();

        $book->refresh();
        $this->assertNull($book->discount_price);
        $this->assertNull($book->discount_starts_at);
        $this->assertNull($book->discount_ends_at);
    }

    public function test_catalog_shows_discounted_price_for_published_book(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'katalog-diskon');

        $book->update([
            'discount_price' => 60000,
            'discount_starts_at' => now()->subHour(),
            'discount_ends_at' => now()->addHour(),
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Catalog/Index')
                ->where('books.data.0.effective_price', 60000)
                ->where('books.data.0.base_price', 100000)
                ->where('books.data.0.discount_source', 'diskon')
                ->where('books.data.0.on_promo', true));
    }
}
