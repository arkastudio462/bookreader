<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Progres baca lintas perangkat (PRD 7.5)
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('location')->nullable();      // CFI (epub) / page number (pdf)
            $table->unsignedTinyInteger('percent')->default(0);
            $table->string('device')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
        });

        // Bookmark posisi baca
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('location');
            $table->string('label')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'book_id']);
        });

        // Highlight teks
        Schema::create('highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('location');
            $table->text('text');
            $table->string('color', 20)->default('yellow');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'book_id']);
        });

        // Preferensi baca per pengguna
        Schema::create('reading_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('theme', 20)->default('light');   // light|sepia|dark
            $table->unsignedSmallInteger('font_size')->default(16);
            $table->string('font_family', 40)->default('serif');
            $table->decimal('line_height', 3, 2)->default(1.60);
            $table->string('margin', 20)->default('normal');  // narrow|normal|wide
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_preferences');
        Schema::dropIfExists('highlights');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('reading_progress');
    }
};
