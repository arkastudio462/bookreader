<?php

namespace App\Services;

use App\Enums\DiscountSource;
use App\Models\Book;
use App\Models\FlashSaleItem;
use Illuminate\Support\Collection;

/**
 * Resolusi harga efektif terpusat (PRD 7.1).
 *
 * Prioritas:
 *   1. Flash sale aktif  -> flash_sale_items.sale_price
 *   2. Diskon terjadwal  -> books.discount_price (bila dalam jadwal)
 *   3. Harga dasar       -> books.price
 *
 * Dipakai katalog, landing, checkout, dan (nanti) perhitungan royalti supaya
 * harga selalu konsisten di semua jalur.
 */
class PriceResolver
{
    /**
     * @return array{price: float, base: float, source: DiscountSource, percent: int}
     */
    public function resolve(Book $book): array
    {
        $base = (float) $book->price;

        // 1. Flash sale = prioritas tertinggi (FR-4.7.10).
        $item = $this->activeFlashSaleItem($book);
        if ($item !== null && (float) $item->sale_price < $base) {
            return $this->info((float) $item->sale_price, DiscountSource::FLASH, $base);
        }

        // 2. Diskon terjadwal.
        if ($book->hasActiveDiscount() && (float) $book->discount_price < $base) {
            return $this->info((float) $book->discount_price, DiscountSource::DISCOUNT, $base);
        }

        return $this->info($base, DiscountSource::NONE, $base);
    }

    public function effectivePrice(Book $book): float
    {
        return $this->resolve($book)['price'];
    }

    public function discountSource(Book $book): DiscountSource
    {
        return $this->resolve($book)['source'];
    }

    /**
     * Tempelkan atribut harga efektif ke model agar langsung tampil di UI.
     */
    public function decorate(Book $book): Book
    {
        $info = $this->resolve($book);

        $book->setAttribute('effective_price', $info['price']);
        $book->setAttribute('base_price', $info['base']);
        $book->setAttribute('discount_source', $info['source']->value);
        $book->setAttribute('discount_percent', $info['percent']);
        $book->setAttribute('on_promo', $info['source'] !== DiscountSource::NONE);

        return $book;
    }

    /**
     * @param  Collection<int, Book>  $books
     * @return Collection<int, Book>
     */
    public function decorateMany(Collection $books): Collection
    {
        return $books->each(fn (Book $book) => $this->decorate($book));
    }

    private function activeFlashSaleItem(Book $book): ?FlashSaleItem
    {
        if ($book->relationLoaded('flashSaleItems')) {
            return $book->flashSaleItems
                ->first(fn (FlashSaleItem $item) => $item->isActiveNow());
        }

        return $book->flashSaleItems()
            ->with('flashSale')
            ->get()
            ->first(fn (FlashSaleItem $item) => $item->isActiveNow());
    }

    /**
     * @return array{price: float, base: float, source: DiscountSource, percent: int}
     */
    private function info(float $price, DiscountSource $source, float $base): array
    {
        $percent = ($base > 0 && $price < $base)
            ? (int) round((1 - $price / $base) * 100)
            : 0;

        return [
            'price' => $price,
            'base' => $base,
            'source' => $source,
            'percent' => max(0, $percent),
        ];
    }
}
