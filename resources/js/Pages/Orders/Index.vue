<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
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
    pending: 'bg-soft text-muted',
    paid: 'bg-soft text-ink',
    failed: 'bg-soft text-muted',
    refunded: 'bg-soft text-faint',
};
</script>

<template>
    <Head title="Pesanan - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl">Pesanan Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div v-if="!orders.length" class="card p-12 text-center">
                    <p class="text-muted">Belum ada pesanan.</p>
                    <Link :href="route('catalog.index')" class="lk mt-4 inline-block text-ink hover:underline">
                        Mulai belanja
                    </Link>
                </div>

                <div v-else class="space-y-3">
                    <Link
                        v-for="order in orders"
                        :key="order.id"
                        :href="route('orders.show', order.id)"
                        class="card flex items-center justify-between p-4"
                    >
                        <div>
                            <p class="lk text-ink">Pesanan #{{ order.id }}</p>
                            <p class="lt text-sm text-muted">
                                {{ order.items_count }} buku · {{ order.created_at }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="lk text-ink">{{ rupiah(order.total) }}</p>
                            <span
                                class="badge-overlay mt-1 inline-block px-2 py-0.5 text-xs"
                                :class="colors[order.status]"
                            >
                                {{ labels[order.status] || order.status }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
