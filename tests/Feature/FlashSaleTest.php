<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\DiscountSource;
use App\Enums\FlashSaleStatus;
use App\Enums\UserRole;
use App\Jobs\NotifyWishlistDiscount;
use App\Models\Book;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\User;
use App\Services\PriceResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 6 — Flash Sale (PRD 4.7).
 */
class FlashSaleTest extends TestCase
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

    private function runningCampaign(array $overrides = []): FlashSale
    {
        return FlashSale::create(array_merge([
            'name' => 'Flash Sale Malam',
            'banner_text' => 'Diskon kilat',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(2),
            'status' => FlashSaleStatus::ACTIVE,
        ], $overrides));
    }

    public function test_flash_sale_price_overrides_scheduled_discount(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'flash-prioritas');

        $book->update([
            'discount_price' => 80000,
            'discount_starts_at' => now()->subDay(),
            'discount_ends_at' => now()->addDay(),
        ]);

        $campaign = $this->runningCampaign();
        $campaign->items()->create(['book_id' => $book->id, 'sale_price' => 50000]);

        $info = app(PriceResolver::class)->resolve($book->fresh());

        $this->assertSame(50000.0, $info['price']);
        $this->assertSame(DiscountSource::FLASH, $info['source']);
        $this->assertSame(50, $info['percent']);
    }

    public function test_sold_out_flash_item_falls_back_to_scheduled_discount(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'flash-habis');

        $book->update([
            'discount_price' => 80000,
            'discount_starts_at' => now()->subDay(),
            'discount_ends_at' => now()->addDay(),
        ]);

        $campaign = $this->runningCampaign();
        $campaign->items()->create([
            'book_id' => $book->id,
            'sale_price' => 50000,
            'sold_count' => 10,
            'stock_limit' => 10,
        ]);

        $info = app(PriceResolver::class)->resolve($book->fresh());

        $this->assertSame(80000.0, $info['price']);
        $this->assertSame(DiscountSource::DISCOUNT, $info['source']);
    }

    public function test_landing_page_exposes_running_flash_sale_with_countdown(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $book = $this->book($author, 100000, 'flash-landing');

        $campaign = $this->runningCampaign(['name' => 'Kilat Landing']);
        $campaign->items()->create(['book_id' => $book->id, 'sale_price' => 60000, 'stock_limit' => 20]);

        $this->get(route('landing'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Landing')
                ->where('flashSale.name', 'Kilat Landing')
                ->has('flashSale.items', 1)
                ->where('flashSale.items.0.book.effective_price', 60000)
                ->where('flashSale.items.0.book.on_promo', true));
    }

    public function test_catalog_promo_filter_returns_only_promoted_books(): void
    {
        $author = User::factory()->create(['role' => UserRole::AUTHOR]);
        $promo = $this->book($author, 100000, 'promo-ya');
        $this->book($author, 90000, 'promo-tidak');

        $campaign = $this->runningCampaign();
        $campaign->items()->create(['book_id' => $promo->id, 'sale_price' => 70000]);

        $this->get(route('catalog.index', ['promo' => 1]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Catalog/Index')
                ->has('books.data', 1)
                ->where('books.data.0.slug', 'promo-ya')
                ->where('books.data.0.on_promo', true));
    }

    public function test_admin_can_create_flash_sale_campaign(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $book = $this->book($admin, 100000, 'admin-flash');

        $response = $this->actingAs($admin)->post(route('admin.flash-sale.store'), [
            'name' => 'Kampanye Uji',
            'banner_text' => 'Serba murah',
            'starts_at' => now()->subMinute()->toDateTimeString(),
            'ends_at' => now()->addDay()->toDateTimeString(),
            'items' => [
                ['book_id' => $book->id, 'sale_price' => 60000, 'stock_limit' => 15],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('flash_sales', [
            'name' => 'Kampanye Uji',
            'status' => FlashSaleStatus::ACTIVE->value,
        ]);
        $this->assertDatabaseHas('flash_sale_items', [
            'book_id' => $book->id,
            'sale_price' => 60000,
        ]);

        Queue::assertPushed(NotifyWishlistDiscount::class);
    }

    public function test_admin_cannot_exceed_max_discount_percent(): void
    {
        config(['perpuskita.promo.max_discount_percent' => 50]);

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $book = $this->book($admin, 100000, 'admin-batas');

        $response = $this->actingAs($admin)->post(route('admin.flash-sale.store'), [
            'name' => 'Terlalu Murah',
            'starts_at' => now()->subMinute()->toDateTimeString(),
            'ends_at' => now()->addDay()->toDateTimeString(),
            'items' => [
                ['book_id' => $book->id, 'sale_price' => 10000],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.sale_price');
        $this->assertDatabaseCount('flash_sales', 0);
    }

    public function test_close_flash_sales_command_updates_statuses(): void
    {
        $expired = $this->runningCampaign([
            'name' => 'Sudah Lewat',
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subHour(),
        ]);

        $scheduled = $this->runningCampaign([
            'name' => 'Mulai Sekarang',
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addHour(),
            'status' => FlashSaleStatus::DRAFT,
        ]);

        $this->artisan('promo:close-flash-sales')->assertSuccessful();

        $this->assertSame(FlashSaleStatus::ENDED, $expired->fresh()->status);
        $this->assertSame(FlashSaleStatus::ACTIVE, $scheduled->fresh()->status);
    }

    public function test_admin_can_end_campaign_and_price_returns_to_normal(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $book = $this->book($admin, 100000, 'admin-akhiri');

        $campaign = $this->runningCampaign();
        $campaign->items()->create(['book_id' => $book->id, 'sale_price' => 40000]);

        $this->assertSame(40000.0, app(PriceResolver::class)->effectivePrice($book->fresh()));

        $this->actingAs($admin)
            ->post(route('admin.flash-sale.end', $campaign->id))
            ->assertSessionHasNoErrors();

        $this->assertSame(FlashSaleStatus::ENDED, $campaign->fresh()->status);
        $this->assertSame(100000.0, app(PriceResolver::class)->effectivePrice($book->fresh()));
    }
}
