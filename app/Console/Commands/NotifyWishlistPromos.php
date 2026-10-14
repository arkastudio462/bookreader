<?php

namespace App\Console\Commands;

use App\Enums\DiscountSource;
use App\Models\Book;
use App\Jobs\NotifyWishlistDiscount;
use App\Services\PriceResolver;
use Illuminate\Console\Command;

/**
 * Pemindai promo -> notifikasi wishlist (FR-4.8.2).
 * Dijadwalkan berkala; juga dapat dipanggil manual.
 */
class NotifyWishlistPromos extends Command
{
    protected $signature = 'promo:notify-wishlist';

    protected $description = 'Kirim notifikasi ke pemilik wishlist untuk buku yang sedang promo';

    public function handle(PriceResolver $prices): int
    {
        $dispatched = 0;

        Book::published()
            ->with('flashSaleItems.flashSale')
            ->chunkById(100, function ($books) use ($prices, &$dispatched) {
                foreach ($books as $book) {
                    if ($prices->resolve($book)['source'] === DiscountSource::NONE) {
                        continue;
                    }

                    NotifyWishlistDiscount::dispatch($book);
                    $dispatched++;
                }
            });

        $this->info("Notifikasi promo dikirim untuk {$dispatched} buku.");

        return self::SUCCESS;
    }
}
