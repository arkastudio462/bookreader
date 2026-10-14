<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Mail\OrderReceiptMail;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentWebhook;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\Voucher;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
        config([
            'perpuskita.midtrans.server_key' => 'SB-Mid-server-TESTKEY',
            'perpuskita.points.earn_percent' => 1.0,
            'perpuskita.points.redeem_rate' => 1.0,
            'perpuskita.points.max_redeem_percent' => 50.0,
        ]);
    }

    private function category(): Category
    {
        return Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function makeBook(User $owner, float $price, string $slug, BookStatus $status = BookStatus::PUBLISHED): Book
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

        Storage::disk('r2')->put($book->s3_key, 'dummy-epub');

        return $book;
    }

    private function voucher(string $code = 'HEMAT10', string $type = 'percent', float $value = 10): Voucher
    {
        return Voucher::create([
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'min_spend' => 0,
            'max_discount' => null,
            'quota' => null,
            'used_count' => 0,
            'scope' => 'global',
            'status' => 'active',
        ]);
    }

    private function webhookPayload(Order $order, string $status, string $code = '200'): array
    {
        $gross = number_format((float) $order->total, 2, '.', '');
        $payload = [
            'order_id' => $order->midtrans_order_id,
            'status_code' => $code,
            'gross_amount' => $gross,
            'transaction_status' => $status,
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'SB-Mid-server-TESTKEY'
        );

        return $payload;
    }

    /* ---------------- Keranjang ---------------- */

    public function test_cart_store_adds_book(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 100000, 'keranjang-1');

        $this->actingAs($user)
            ->post(route('cart.store'), ['book_id' => $book->id])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['book_id' => $book->id]);
    }

    public function test_cart_store_rejects_owned_book(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 100000, 'keranjang-owned');

        // Tandai buku sudah dimiliki lewat order paid.
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => 100000,
            'total' => 100000,
            'status' => OrderStatus::PAID,
            'midtrans_order_id' => 'ORD-OWNED-1',
        ]);
        $order->items()->create(['book_id' => $book->id, 'price' => 100000]);

        $this->actingAs($user)
            ->post(route('cart.store'), ['book_id' => $book->id])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('cart_items', ['book_id' => $book->id]);
    }

    public function test_cart_destroy_removes_item(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 100000, 'keranjang-hapus');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 100000]);

        $this->actingAs($user)
            ->delete(route('cart.destroy', $book->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['book_id' => $book->id]);
    }

    /* ---------------- CheckoutService ---------------- */

    public function test_checkout_service_computes_voucher_and_point_discounts(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER, 'points_balance' => 30000]);
        $book = $this->makeBook($owner, 100000, 'checkout-hitung');
        $this->voucher('HEMAT10');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 100000]);

        $order = app(CheckoutService::class)->place($user, 'HEMAT10', true);

        $this->assertSame('pending', $order->status->value);
        $this->assertEquals(100000.0, (float) $order->subtotal);
        $this->assertEquals(10000.0, (float) $order->voucher_discount);
        // Maks potongan poin 50% dari 90000 = 45000, tapi saldo hanya 30000.
        $this->assertEquals(30000.0, (float) $order->point_discount);
        $this->assertEquals(60000.0, (float) $order->total);
        $this->assertSame(30000, (int) $order->points_used);

        // Saldo & ledger poin terpotong, keranjang dikosongkan.
        $this->assertSame(0, (int) $user->fresh()->points_balance);
        $this->assertSame(1, PointTransaction::where('type', 'spend')->count());
        $this->assertSame(0, $cart->items()->count());

        // Voucher tercatat terpakai.
        $this->assertSame(1, (int) Voucher::where('code', 'HEMAT10')->first()->used_count);
    }

    public function test_checkout_service_rejects_empty_cart(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(CheckoutService::class)->place($user, null, false);
    }

    /* ---------------- Checkout endpoint + Snap ---------------- */

    public function test_checkout_endpoint_creates_order_and_snap_token(): void
    {
        Http::fake(['*' => Http::response(['token' => 'snap-token-abc', 'redirect_url' => 'https://snap'], 200)]);

        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, 'checkout-endpoint');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 50000]);

        $this->actingAs($user)
            ->post(route('checkout.store'), ['voucher_code' => '', 'use_points' => false])
            ->assertRedirect();

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame('snap-token-abc', $order->snap_token);
        $this->assertEquals(50000.0, (float) $order->total);
    }

    /* ---------------- Webhook ---------------- */

    public function test_webhook_settlement_marks_paid_and_grants_library_access(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, 'webhook-paid');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 50000]);

        $order = app(CheckoutService::class)->place($user, null, false);

        $this->postJson(route('midtrans.callback'), $this->webhookPayload($order, 'settlement'))
            ->assertOk()
            ->assertJson(['status' => 'processed']);

        $order->refresh();
        $this->assertSame(OrderStatus::PAID, $order->status);
        $this->assertNotNull($order->paid_at);

        // Buku masuk Pustaka & poin pembelian bertambah (1% dari 50000 = 500).
        $this->assertTrue($user->fresh()->ownsBook($book->id));
        $this->assertSame(500, (int) $user->fresh()->points_balance);
        Mail::assertSent(OrderReceiptMail::class);
    }

    public function test_webhook_is_idempotent(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, 'webhook-idempotent');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 50000]);
        $order = app(CheckoutService::class)->place($user, null, false);

        $payload = $this->webhookPayload($order, 'settlement');

        $this->postJson(route('midtrans.callback'), $payload)->assertOk();
        $this->postJson(route('midtrans.callback'), $payload)
            ->assertOk()
            ->assertJson(['status' => 'duplicate']);

        $this->assertSame(1, PaymentWebhook::count());
        // Poin hanya diberikan sekali.
        $this->assertSame(500, (int) $user->fresh()->points_balance);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, 'webhook-badsig');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 50000]);
        $order = app(CheckoutService::class)->place($user, null, false);

        $payload = $this->webhookPayload($order, 'settlement');
        $payload['signature_key'] = 'tidak-valid';

        $this->postJson(route('midtrans.callback'), $payload)
            ->assertStatus(403)
            ->assertJson(['status' => 'invalid_signature']);

        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
    }

    public function test_webhook_expire_reverts_points_and_releases_voucher(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER, 'points_balance' => 10000]);
        $book = $this->makeBook($owner, 100000, 'webhook-expire');
        $this->voucher('KEDALUWARSA');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 100000]);

        $order = app(CheckoutService::class)->place($user, 'KEDALUWARSA', true);

        $this->assertSame(0, (int) $user->fresh()->points_balance);
        $this->assertSame(1, (int) Voucher::first()->used_count);

        $this->postJson(route('midtrans.callback'), $this->webhookPayload($order, 'expire'))
            ->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::FAILED, $order->status);
        // Poin dikembalikan & kuota voucher dilepas.
        $this->assertSame(10000, (int) $user->fresh()->points_balance);
        $this->assertSame(0, (int) Voucher::first()->used_count);
    }

    public function test_webhook_refund_deducts_earned_points(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $book = $this->makeBook($owner, 50000, 'webhook-refund');

        $cart = Cart::forUser($user);
        $cart->items()->create(['book_id' => $book->id, 'price_snapshot' => 50000]);
        $order = app(CheckoutService::class)->place($user, null, false);

        $this->postJson(route('midtrans.callback'), $this->webhookPayload($order, 'settlement'))->assertOk();
        $this->assertSame(500, (int) $user->fresh()->points_balance);

        $this->postJson(route('midtrans.callback'), $this->webhookPayload($order, 'refund'))->assertOk();

        $this->assertSame(OrderStatus::REFUNDED, $order->fresh()->status);
        $this->assertSame(0, (int) $user->fresh()->points_balance);
    }

    /* ---------------- Pustaka & halaman ---------------- */

    public function test_library_index_lists_owned_books(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $owned = $this->makeBook($owner, 50000, 'pustaka-owned');
        $other = $this->makeBook($owner, 50000, 'pustaka-other');

        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => 50000,
            'total' => 50000,
            'status' => OrderStatus::PAID,
            'midtrans_order_id' => 'ORD-LIB-1',
        ]);
        $order->items()->create(['book_id' => $owned->id, 'price' => 50000]);

        $this->actingAs($user)
            ->get(route('library.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Library/Index')
                ->has('books', 1)
                ->where('books.0.id', $owned->id));

        $this->assertNotSame($owned->id, $other->id);
    }

    public function test_order_show_is_forbidden_for_other_user(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AUTHOR]);
        $user = User::factory()->create(['role' => UserRole::USER]);
        $intruder = User::factory()->create(['role' => UserRole::USER]);

        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => 50000,
            'total' => 50000,
            'status' => OrderStatus::PENDING,
            'midtrans_order_id' => 'ORD-SECRET-1',
        ]);

        $this->actingAs($intruder)
            ->get(route('orders.show', $order->id))
            ->assertStatus(403);
    }

    public function test_points_page_shows_ledger(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER, 'points_balance' => 250]);

        PointTransaction::create([
            'user_id' => $user->id,
            'type' => 'earn',
            'points' => 250,
            'balance_after' => 250,
            'source' => 'order',
            'note' => 'Poin pembelian',
        ]);

        $this->actingAs($user)
            ->get(route('points.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Points/Index')
                ->where('balance', 250)
                ->has('transactions', 1));
    }
}
