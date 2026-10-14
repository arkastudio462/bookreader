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
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-200 text-gray-800',
};
</script>

<template>
    <Head title="Pesanan - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Pesanan Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div v-if="!orders.length" class="rounded-lg bg-white p-12 text-center shadow">
                    <p class="text-gray-600">Belum ada pesanan.</p>
                    <Link :href="route('catalog.index')" class="mt-4 inline-block text-blue-600 hover:underline">
                        Mulai belanja
                    </Link>
                </div>

                <div v-else class="space-y-3">
                    <Link
                        v-for="order in orders"
                        :key="order.id"
                        :href="route('orders.show', order.id)"
                        class="flex items-center justify-between rounded-lg bg-white p-4 shadow hover:shadow-md"
                    >
                        <div>
                            <p class="font-medium text-gray-900">Pesanan #{{ order.id }}</p>
                            <p class="text-sm text-gray-600">
                                {{ order.items_count }} buku · {{ order.created_at }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ rupiah(order.total) }}</p>
                            <span
                                class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium"
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
