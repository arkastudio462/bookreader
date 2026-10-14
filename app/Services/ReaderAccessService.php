<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ReaderAccessService
{
    public const MODE_FULL = 'full';
    public const MODE_SAMPLE = 'sample';

    /**
     * Tentukan mode akses baca untuk user terhadap buku (PRD 7.5).
     *
     * @return array{mode: ?string, reason: string}
     */
    public function resolve(?User $user, Book $book): array
    {
        if (!$user) {
            return ['mode' => null, 'reason' => 'unauthenticated'];
        }

        // Admin & pemilik (penulis/penerbit) boleh membaca penuh.
        if ($user->hasRole('admin') || $book->publisher_id === $user->id) {
            return ['mode' => self::MODE_FULL, 'reason' => 'owner_or_admin'];
        }

        // Buku belum terbit hanya untuk pemilik/admin (sudah ditangani di atas).
        if ($book->status !== BookStatus::PUBLISHED) {
            return ['mode' => null, 'reason' => 'not_published'];
        }

        // Buku gratis dibaca penuh oleh siapa pun yang login.
        if ((float) $book->price === 0.0) {
            return ['mode' => self::MODE_FULL, 'reason' => 'free'];
        }

        // Pembeli (order paid) membaca penuh. Aktif setelah Fase 5 (tabel orders).
        if ($this->hasPurchased($user, $book)) {
            return ['mode' => self::MODE_FULL, 'reason' => 'purchased'];
        }

        // Sampel gratis untuk buku berbayar yang terbit.
        return ['mode' => self::MODE_SAMPLE, 'reason' => 'sample'];
    }

    public function canRead(?User $user, Book $book): bool
    {
        return $this->resolve($user, $book)['mode'] === self::MODE_FULL;
    }

    public function canSample(?User $user, Book $book): bool
    {
        return $this->resolve($user, $book)['mode'] === self::MODE_SAMPLE;
    }

    /**
     * Cek kepemilikan lewat order paid. Aman bila tabel orders belum ada (pra-Fase 5).
     */
    protected function hasPurchased(User $user, Book $book): bool
    {
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            return false;
        }

        return \DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', 'paid')
            ->where('order_items.book_id', $book->id)
            ->exists();
    }
}
