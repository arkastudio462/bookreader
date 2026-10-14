<?php

return [

    'currency' => 'IDR',

    /*
    |--------------------------------------------------------------------------
    | Poin Reward (PRD 4.7.d)
    |--------------------------------------------------------------------------
    | Poin non-tunai: diperoleh dari pembelian dan hanya bisa dipotongkan
    | saat checkout.
    */
    'points' => [
        // Persentase poin yang diperoleh dari total pembelian.
        'earn_percent' => (float) env('POINTS_EARN_PERCENT', 1.0),
        // Nilai tukar: 1 poin = berapa rupiah saat dipotongkan.
        'redeem_rate' => (float) env('POINTS_REDEEM_RATE', 1.0),
        // Batas maksimum potongan poin terhadap subtotal (%).
        'max_redeem_percent' => (float) env('POINTS_MAX_REDEEM_PERCENT', 50.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Midtrans Snap (PRD 4.6.3)
    |--------------------------------------------------------------------------
    */
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
        'snap_url' => env('MIDTRANS_SNAP_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Promo (PRD 4.7)
    |--------------------------------------------------------------------------
    | Batas maksimum diskon per buku yang boleh diatur penulis/penerbit.
    */
    'promo' => [
        'max_discount_percent' => (float) env('PROMO_MAX_DISCOUNT_PERCENT', 80.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Order
    |--------------------------------------------------------------------------
    */
    'order' => [
        'expiry_minutes' => (int) env('ORDER_EXPIRY_MINUTES', 60),
    ],

];
