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
            <h2 class="h text-xl">Detail Pesanan #{{ order.id }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="flashError" class="card border-hair px-4 py-3 text-sm text-ink">
                    {{ flashError }}
                </div>
                <div v-if="flashSuccess" class="card border-hair px-4 py-3 text-sm text-ink">
                    {{ flashSuccess }}
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Status</span>
                        <span class="lk text-ink">{{ labels[order.status] || order.status }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-muted">ID Midtrans</span>
                        <span class="font-mono text-sm text-ink">{{ order.midtrans_order_id }}</span>
                    </div>
                    <div v-if="order.paid_at" class="mt-2 flex items-center justify-between">
                        <span class="text-muted">Dibayar pada</span>
                        <span class="text-ink">{{ order.paid_at }}</span>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="lk mb-4 text-ink">Item</h3>
                    <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between border-b border-hairline-soft py-2 last:border-0">
                        <div>
                            <p class="lk text-ink">{{ item.book?.title || ('Buku #' + item.book_id) }}</p>
                            <p class="lt text-xs text-muted">Harga dasar {{ rupiah(item.price) }}</p>
                        </div>
                        <span class="text-ink">{{ rupiah(item.price) }}</span>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex justify-between py-1 text-muted">
                        <span>Subtotal</span>
                        <span>{{ rupiah(order.subtotal) }}</span>
                    </div>
                    <div v-if="Number(order.voucher_discount) > 0" class="flex justify-between py-1 text-muted">
                        <span>Voucher</span>
                        <span>−{{ rupiah(order.voucher_discount) }}</span>
                    </div>
                    <div v-if="Number(order.point_discount) > 0" class="flex justify-between py-1 text-muted">
                        <span>Poin ({{ order.points_used }} poin)</span>
                        <span>−{{ rupiah(order.point_discount) }}</span>
                    </div>
                    <div class="lk mt-3 flex justify-between border-t border-hair pt-3 text-lg text-ink">
                        <span>Total</span>
                        <span>{{ rupiah(order.total) }}</span>
                    </div>

                    <template v-if="order.status === 'pending'">
                        <button
                            v-if="order.snap_token"
                            @click="payNow"
                            class="btn-primary mt-4 w-full py-3"
                        >
                            Bayar Sekarang
                        </button>
                        <button
                            v-else
                            @click="refreshToken"
                            class="btn-primary mt-4 w-full py-3"
                        >
                            Muat Halaman Pembayaran
                        </button>
                    </template>

                    <Link
                        v-if="order.status === 'paid'"
                        :href="route('library.index')"
                        class="btn-primary mt-4 block py-3 text-center"
                    >
                        Buka Pustaka Saya
                    </Link>
                </div>

                <Link :href="route('orders.index')" class="lk block text-center text-sm text-ink hover:underline">
                    ← Kembali ke daftar pesanan
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
