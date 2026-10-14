<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi saat buku di wishlist sedang diskon / masuk flash sale
 * (FR-4.8.2).
 */
class WishlistDiscountNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Book $book,
        public float $effectivePrice,
        public int $percent,
        public string $source,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'wishlist_discount',
            'book_id' => $this->book->id,
            'title' => $this->book->title,
            'slug' => $this->book->slug,
            'percent' => $this->percent,
            'effective_price' => $this->effectivePrice,
            'base_price' => (float) $this->book->price,
            'source' => $this->source,
            'message' => $this->message(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Buku wishlist Anda sedang diskon!')
            ->greeting('Halo ' . ($notifiable->name ?? 'Pembaca') . '!')
            ->line($this->message())
            ->line('Harga: Rp ' . number_format($this->effectivePrice, 0, ',', '.'))
            ->action('Lihat Buku', url('/buku/' . $this->book->slug))
            ->line('Jangan lewatkan sebelum promo berakhir.');
    }

    private function message(): string
    {
        $label = $this->source === 'flash' ? 'Flash Sale' : 'diskon';

        return sprintf(
            'Buku "%s" yang ada di wishlist Anda kini %s %d%%.',
            $this->book->title,
            $label,
            $this->percent,
        );
    }
}
