<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pesanan</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h1 style="font-size: 20px;">Terima kasih atas pembelian Anda</h1>
    <p>Halo {{ $order->user->name ?? 'Pembaca' }},</p>
    <p>Pembayaran pesanan <strong>#{{ $order->id }}</strong> telah kami terima. Buku sudah masuk ke Pustaka Anda.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <thead>
            <tr>
                <th style="text-align: left; border-bottom: 1px solid #e5e7eb; padding: 6px 0;">Buku</th>
                <th style="text-align: right; border-bottom: 1px solid #e5e7eb; padding: 6px 0;">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td style="padding: 6px 0;">{{ $item->book->title ?? 'Buku #' . $item->book_id }}</td>
                    <td style="text-align: right; padding: 6px 0;">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Subtotal: Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</p>
    @if ((float) $order->voucher_discount > 0)
        <p>Voucher: −Rp {{ number_format((float) $order->voucher_discount, 0, ',', '.') }}</p>
    @endif
    @if ((float) $order->point_discount > 0)
        <p>Poin: −Rp {{ number_format((float) $order->point_discount, 0, ',', '.') }}</p>
    @endif
    <p style="font-size: 16px;"><strong>Total: Rp {{ number_format((float) $order->total, 0, ',', '.') }}</strong></p>

    <p>Selamat membaca!<br>— PerpusKita</p>
</body>
</html>
