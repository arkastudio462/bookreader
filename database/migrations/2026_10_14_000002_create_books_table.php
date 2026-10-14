<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publisher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('author_name');
            $table->string('isbn')->nullable()->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sample_price', 10, 2)->default(0);
            $table->string('cover_path')->nullable();
            $table->string('s3_key')->nullable();
            $table->unsignedInteger('pages')->nullable();
            $table->string('language')->default('id');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('genre_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected', 'archived'])
                ->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['publisher_id', 'status']);
            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
