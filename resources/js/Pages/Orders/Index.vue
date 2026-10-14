<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    orders: Array,
});

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');

const labels = {
    pending: 'Menunggu Pembayaran',
    paid: 'Dibayar',
    failed: 'Gagal',
    refunded: 'Dikembalikan',
};

const colors = {
    pending: 'bg-amber-50 text-amber-900 border-amber-200',
    paid: 'bg-emerald-50 text-emerald-900 border-emerald-200',
    failed: 'bg-rose-50 text-rose-900 border-rose-200',
    refunded: 'bg-soft text-muted border-hair',
};

const dots = {
    pending: 'bg-amber-600',
    paid: 'bg-emerald-600',
    failed: 'bg-error',
    refunded: 'bg-faint',
};

const activeFilter = ref('all');

const filters = [
    { id: 'all', label: 'Semua' },
    { id: 'pending', label: 'Menunggu Bayar' },
    { id: 'paid', label: 'Dibayar' },
    { id: 'refunded', label: 'Dikembalikan' },
];

const filteredOrders = computed(() =>
    activeFilter.value === 'all' ? props.orders : props.orders.filter((o) => o.status === activeFilter.value),
);
</script>

<template>
    <Head title="Pesanan - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Pesanan Saya</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Akun</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Pesanan</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Riwayat Pesanan</h1>
                    <p class="text-body-md text-muted">Lacak status pembayaran dan akses buku yang sudah Anda beli.</p>
                </div>
                <Link :href="route('catalog.index')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    Beli Lagi
                </Link>
            </header>

            <div class="flex flex-wrap gap-space-xs">
                <button
                    v-for="f in filters"
                    :key="f.id"
                    type="button"
                    class="rounded-full px-space-md py-1.5 text-body-sm transition-colors"
                    :class="activeFilter === f.id ? 'bg-ink text-white' : 'bg-soft text-ink hover:bg-hair'"
                    @click="activeFilter = f.id"
                >
                    {{ f.label }}
                </button>
            </div>

            <div v-if="!orders.length" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[28px] text-muted">receipt_long</span>
                </div>
                <h2 class="mt-space-md text-title-lg text-ink">Belum ada pesanan</h2>
                <p class="mx-auto mt-space-xs max-w-md text-body-md text-muted">Semua transaksi Anda akan tercatat di sini beserta status pembayarannya.</p>
                <Link :href="route('catalog.index')" class="btn-primary mt-space-lg gap-2 px-space-lg">
                    Mulai Belanja
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </Link>
            </div>

            <div v-else-if="!filteredOrders.length" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[28px] text-muted">filter_alt_off</span>
                </div>
                <h2 class="mt-space-md text-title-lg text-ink">Tidak ada pesanan berstatus ini</h2>
                <p class="mt-space-xs text-body-md text-muted">Pilih filter lain untuk melihat pesanan lainnya.</p>
                <button type="button" class="btn-outline mt-space-lg text-sm" @click="activeFilter = 'all'">Tampilkan Semua</button>
            </div>

            <section v-else class="overflow-hidden rounded-[24px] border border-hair bg-canvas">
                <div class="hidden border-b border-hairline-soft px-space-lg py-space-sm text-muted text-caption uppercase md:grid md:grid-cols-12">
                    <span class="col-span-4">Pesanan</span>
                    <span class="col-span-2">Tanggal</span>
                    <span class="col-span-2">Item</span>
                    <span class="col-span-2">Total</span>
                    <span class="col-span-2 text-right">Status</span>
                </div>

                <Link
                    v-for="order in filteredOrders"
                    :key="order.id"
                    :href="route('orders.show', order.id)"
                    class="grid grid-cols-1 gap-space-sm border-b border-hairline-soft px-space-lg py-space-md transition-colors last:border-0 hover:bg-soft md:grid-cols-12 md:items-center"
                >
                    <div class="col-span-4 flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px] text-muted">receipt</span>
                        <span class="text-action text-ink">Pesanan #{{ order.id }}</span>
                    </div>
                    <span class="col-span-2 text-body-sm text-muted">{{ order.created_at }}</span>
                    <span class="col-span-2 text-body-sm text-muted">{{ order.items_count }} buku</span>
                    <span class="col-span-2 text-body-md text-ink">{{ rupiah(order.total) }}</span>
                    <div class="col-span-2 flex justify-start md:justify-end">
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-badge-label" :class="colors[order.status]">
                            <span class="h-1.5 w-1.5 rounded-full" :class="dots[order.status]"></span>
                            {{ labels[order.status] || order.status }}
                        </span>
                    </div>
                </Link>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
