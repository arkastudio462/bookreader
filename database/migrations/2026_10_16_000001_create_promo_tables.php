<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 6 — Flash Sale & Diskon (PRD 4.7, 6.3).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Diskon terjadwal per buku (FR-4.4.4 / FR-4.7.1)
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->timestamp('discount_starts_at')->nullable()->after('discount_price');
            $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at');
        });

        // Kampanye flash sale (FR-4.7.8)
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('banner_text')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('draft'); // draft|active|ended
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
        });

        // Buku dalam kampanye (harga efektif prioritas tertinggi)
        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->decimal('sale_price', 10, 2);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('stock_limit')->nullable();
            $table->timestamps();

            $table->unique(['flash_sale_id', 'book_id']);
        });

        // Notifikasi in-app bawaan Laravel (FR-4.8.2, disiapkan untuk Fase 10)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('flash_sale_items');
        Schema::dropIfExists('flash_sales');

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'discount_starts_at', 'discount_ends_at']);
        });
    }
};
