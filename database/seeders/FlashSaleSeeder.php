<?php

namespace Database\Seeders;

use App\Enums\FlashSaleStatus;
use App\Models\Book;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use Illuminate\Database\Seeder;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $sale = FlashSale::create([
            'name' => 'Flash Sale Akhir Pekan',
            'banner_text' => 'Harga kembali normal saat hitung mundur selesai.',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(3),
            'status' => FlashSaleStatus::ACTIVE,
        ]);

        $discountedBooks = Book::whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->where('discount_price', '<', 'price')
            ->take(5)
            ->get();

        foreach ($discountedBooks as $index => $book) {
            FlashSaleItem::create([
                'flash_sale_id' => $sale->id,
                'book_id' => $book->id,
                'sale_price' => $book->discount_price,
                'sold_count' => rand(15, 95),
                'stock_limit' => 100,
            ]);
        }
    }
}
