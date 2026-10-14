<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Saldo poin non-tunai pada user (PRD 4.7.d)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points_balance')->default(0)->after('role');
        });

        // Keranjang (PRD 4.6.1)
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_snapshot', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['cart_id', 'book_id']);
        });

        // Voucher (PRD 4.7.b)
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 12, 2);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->enum('scope', ['global', 'publisher', 'book'])->default('global');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'expires_at']);
        });

        // Order (PRD 4.6, 7.2)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('voucher_discount', 12, 2)->default(0);
            $table->decimal('point_discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('snap_token')->nullable();
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('points_used')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2);           // harga efektif saat checkout
            $table->enum('discount_source', ['none', 'diskon', 'flash'])->default('none');
            $table->decimal('author_share', 12, 2)->default(0);
            $table->decimal('publisher_share', 12, 2)->default(0);
            $table->timestamps();
        });

        // Audit pemakaian voucher (PRD 4.7.6)
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        // Idempotensi webhook Midtrans (PRD 4.6.4)
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('midtrans_order_id');
            $table->string('event_type');
            $table->json('payload')->nullable();
            $table->string('signature')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['midtrans_order_id', 'event_type']);
        });

        // Ledger mutasi poin (PRD 4.7.14)
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'spend', 'revert']);
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('source')->nullable();      // order|streak|review
            $table->string('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Refund oleh admin (PRD 4.6.7)
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('points_balance');
        });
    }
};
