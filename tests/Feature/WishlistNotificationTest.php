<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\FlashSaleStatus;
use App\Enums\UserRole;
use App\Jobs\NotifyWishlistDiscount;
use App\Models\Book;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\WishlistDiscountNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 6 — Notifikasi wishlist saat promo (PRD 4.8.2).
 */
class WishlistNotificationTest extends TestCase
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

    public function test_job_notifies_wishlist_owners_when_book_is_promoted(): void
    {
        Notification::fake();

        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->book($author, 100000, 'wishlist-promo');

        Wishlist::create(['user_id' => $reader->id, 'book_id' => $book->id]);

        $book->update([
            'discount_price' => 65000,
            'discount_starts_at' => now()->subHour(),
            'discount_ends_at' => now()->addHour(),
        ]);

        (new NotifyWishlistDiscount($book->fresh()))->handle(app(\App\Services\PriceResolver::class));

        Notification::assertSentTo($reader, WishlistDiscountNotification::class, function ($notification) use ($book) {
            $data = $notification->toArray($book->publisher);

            return $data['book_id'] === $book->id
                && $data['percent'] === 35
                && (float) $data['effective_price'] === 65000.0;
        });
    }

    public function test_job_skips_books_without_active_promo(): void
    {
        Notification::fake();

        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->book($author, 100000, 'wishlist-tanpa-promo');

        Wishlist::create(['user_id' => $reader->id, 'book_id' => $book->id]);

        (new NotifyWishlistDiscount($book->fresh()))->handle(app(\App\Services\PriceResolver::class));

        Notification::assertNothingSent();
    }

    public function test_author_setting_active_discount_dispatches_notification_job(): void
    {
        Notification::fake();

        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->book($author, 100000, 'wishlist-auto');

        Wishlist::create(['user_id' => $reader->id, 'book_id' => $book->id]);

        $this->actingAs($author)->patch(route('author.books.discount', $book->id), [
            'discount_price' => 80000,
            'discount_starts_at' => now()->subMinute()->toDateTimeString(),
            'discount_ends_at' => now()->addDay()->toDateTimeString(),
        ])->assertSessionHasNoErrors();

        // Job dijalankan sinkron pada koneksi sync -> notifikasi terkirim.
        Notification::assertSentTo($reader, WishlistDiscountNotification::class);
    }

    public function test_notify_wishlist_promos_command_dispatches_for_promoted_books(): void
    {
        Notification::fake();

        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->book($author, 100000, 'wishlist-perintah');
        $this->book($author, 50000, 'wishlist-biasa');

        Wishlist::create(['user_id' => $reader->id, 'book_id' => $book->id]);

        $campaign = FlashSale::create([
            'name' => 'Kilat',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'status' => FlashSaleStatus::ACTIVE,
        ]);
        $campaign->items()->create(['book_id' => $book->id, 'sale_price' => 40000]);

        $this->artisan('promo:notify-wishlist')->assertSuccessful();

        Notification::assertSentTo($reader, WishlistDiscountNotification::class);
    }

    public function test_wishlist_page_marks_promoted_books(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $reader = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->book($author, 100000, 'wishlist-tampil');

        Wishlist::create(['user_id' => $reader->id, 'book_id' => $book->id]);

        $campaign = FlashSale::create([
            'name' => 'Kilat Tampil',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'status' => FlashSaleStatus::ACTIVE,
        ]);
        $campaign->items()->create(['book_id' => $book->id, 'sale_price' => 55000]);

        $this->actingAs($reader)->get(route('wishlist.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Wishlist/Index')
                ->has('wishlists', 1)
                ->where('wishlists.0.book.effective_price', 55000)
                ->where('wishlists.0.book.discount_source', 'flash'));
    }
}
