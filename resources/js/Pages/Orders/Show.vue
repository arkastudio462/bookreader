<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    order: Object,
    client_key: String,
    snap_script: String,
});

const page = usePage();
const flashError = computed(() => page.props.flash?.error ?? null);
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');

const labels = {
    pending: 'Menunggu Pembayaran',
    paid: 'Dibayar',
    failed: 'Gagal',
    refunded: 'Dikembalikan',
};

function loadSnap(callback) {
    if (window.snap) {
        callback();
        return;
    }
    const script = document.createElement('script');
    script.src = props.snap_script;
    script.setAttribute('data-client-key', props.client_key || '');
    script.onload = callback;
    document.head.appendChild(script);
}

function payNow() {
    loadSnap(() => {
        window.snap.pay(props.order.snap_token, {
            onSuccess: () => router.reload(),
            onPending: () => router.reload(),
            onError: () => router.reload(),
            onClose: () => {},
        });
    });
}

function refreshToken() {
    router.post(route('orders.pay', props.order.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Pesanan #${order.id} - PerpusKita`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Pesanan #{{ order.id }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="flashError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ flashError }}
                </div>
                <div v-if="flashSuccess" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ flashSuccess }}
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Status</span>
                        <span class="font-semibold text-gray-900">{{ labels[order.status] || order.status }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-gray-600">ID Midtrans</span>
                        <span class="font-mono text-sm text-gray-900">{{ order.midtrans_order_id }}</span>
                    </div>
                    <div v-if="order.paid_at" class="mt-2 flex items-center justify-between">
                        <span class="text-gray-600">Dibayar pada</span>
                        <span class="text-gray-900">{{ order.paid_at }}</span>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 font-semibold text-gray-900">Item</h3>
                    <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between border-b border-gray-100 py-2 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ item.book?.title || ('Buku #' + item.book_id) }}</p>
                            <p class="text-xs text-gray-500">Harga dasar {{ rupiah(item.price) }}</p>
                        </div>
                        <span class="text-gray-900">{{ rupiah(item.price) }}</span>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex justify-between py-1 text-gray-700">
                        <span>Subtotal</span>
                        <span>{{ rupiah(order.subtotal) }}</span>
                    </div>
                    <div v-if="Number(order.voucher_discount) > 0" class="flex justify-between py-1 text-green-700">
                        <span>Voucher</span>
                        <span>−{{ rupiah(order.voucher_discount) }}</span>
                    </div>
                    <div v-if="Number(order.point_discount) > 0" class="flex justify-between py-1 text-green-700">
                        <span>Poin ({{ order.points_used }} poin)</span>
                        <span>−{{ rupiah(order.point_discount) }}</span>
                    </div>
                    <div class="mt-3 flex justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>{{ rupiah(order.total) }}</span>
                    </div>

                    <template v-if="order.status === 'pending'">
                        <button
                            v-if="order.snap_token"
                            @click="payNow"
                            class="mt-4 w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700"
                        >
                            Bayar Sekarang
                        </button>
                        <button
                            v-else
                            @click="refreshToken"
                            class="mt-4 w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700"
                        >
                            Muat Halaman Pembayaran
                        </button>
                    </template>

                    <Link
                        v-if="order.status === 'paid'"
                        :href="route('library.index')"
                        class="mt-4 block rounded-lg bg-green-600 py-3 text-center font-semibold text-white hover:bg-green-700"
                    >
                        Buka Pustaka Saya
                    </Link>
                </div>

                <Link :href="route('orders.index')" class="block text-center text-sm text-blue-600 hover:underline">
                    ← Kembali ke daftar pesanan
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
