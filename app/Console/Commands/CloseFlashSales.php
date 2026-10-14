<?php

namespace App\Console\Commands;

use App\Enums\FlashSaleStatus;
use App\Models\FlashSale;
use Illuminate\Console\Command;

/**
 * Tutup/buka kampanye flash sale sesuai jendela waktu (FR-4.7.9).
 * Dijadwalkan tiap menit agar harga otomatis kembali normal saat berakhir.
 */
class CloseFlashSales extends Command
{
    protected $signature = 'promo:close-flash-sales';

    protected $description = 'Perbarui status kampanye flash sale sesuai jadwal';

    public function handle(): int
    {
        // Kampanye yang jendelanya sudah lewat -> selesai.
        $ended = FlashSale::whereIn('status', [FlashSaleStatus::ACTIVE, FlashSaleStatus::DRAFT])
            ->where('ends_at', '<', now())
            ->update(['status' => FlashSaleStatus::ENDED]);

        // Kampanye terjadwal yang waktunya sudah tiba -> aktif.
        $started = FlashSale::where('status', FlashSaleStatus::DRAFT)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->update(['status' => FlashSaleStatus::ACTIVE]);

        $this->info("Flash sale: {$started} diaktifkan, {$ended} diakhiri.");

        return self::SUCCESS;
    }
}
