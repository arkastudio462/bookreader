<?php

namespace App\Jobs;

use App\Enums\DiscountSource;
use App\Models\Book;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\WishlistDiscountNotification;
use App\Services\PriceResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Kirim notifikasi ke pemilik wishlist ketika sebuah buku sedang promo
 * (FR-4.8.2). Dipakai saat diskon diatur dan oleh scheduler berkala.
 */
class NotifyWishlistDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Book $book)
    {
    }

    public function handle(PriceResolver $prices): void
    {
        $info = $prices->resolve($this->book);

        if ($info['source'] === DiscountSource::NONE) {
            return;
        }

        $userIds = Wishlist::where('book_id', $this->book->id)->pluck('user_id');

        if ($userIds->isEmpty()) {
            return;
        }

        foreach (User::whereIn('id', $userIds)->cursor() as $user) {
            // Hindari spam: lewati bila sudah diberi tahu < 1 hari untuk buku ini.
            $already = $user->notifications()
                ->where('type', WishlistDiscountNotification::class)
                ->where('data->book_id', $this->book->id)
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if ($already) {
                continue;
            }

            $user->notify(new WishlistDiscountNotification(
                $this->book,
                $info['price'],
                $info['percent'],
                $info['source']->value,
            ));
        }
    }
}
