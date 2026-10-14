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

const colors = {
    pending: 'bg-amber-50 text-amber-900 border-amber-200',
    paid: 'bg-emerald-50 text-emerald-900 border-emerald-200',
    failed: 'bg-rose-50 text-rose-900 border-rose-200',
    refunded: 'bg-soft text-muted border-hair',
};

const steps = computed(() => [
    { label: 'Pesanan dibuat', hint: props.order.created_at, done: true },
    { label: 'Pembayaran diterima', hint: props.order.paid_at || 'Menunggu konfirmasi', done: props.order.status === 'paid' },
    {
        label: props.order.status === 'paid' ? 'Buku tersedia di pustaka' : 'Buku diaktifkan',
        hint: props.order.status === 'paid' ? 'Siap dibaca sekarang' : 'Aktif setelah pembayaran lunas',
        done: props.order.status === 'paid',
    },
]);

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
            <h2 class="h text-xl text-ink">Detail Pesanan #{{ order.id }}</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <Link :href="route('orders.index')" class="transition-colors hover:text-ink">Pesanan</Link>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">#{{ order.id }}</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-space-sm">
                        <h1 class="text-headline-sm tracking-tight text-ink">Pesanan #{{ order.id }}</h1>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-badge-label" :class="colors[order.status]">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ labels[order.status] || order.status }}
                        </span>
                    </div>
                    <p class="text-body-md text-muted">
                        ID Midtrans <span class="font-medium text-ink">{{ order.midtrans_order_id }}</span>
                    </p>
                </div>
                <Link :href="route('orders.index')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Semua Pesanan
                </Link>
            </header>

            <div v-if="flashError" class="flex items-start gap-space-sm rounded-[20px] border border-error bg-rose-50 p-space-md text-body-sm text-error">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ flashError }}</span>
            </div>
            <div v-if="flashSuccess" class="flex items-start gap-space-sm rounded-[20px] border border-emerald-200 bg-emerald-50 p-space-md text-body-sm text-emerald-900">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ flashSuccess }}</span>
            </div>

            <div class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <div class="space-y-space-lg lg:col-span-7">
                    <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <h2 class="border-b border-hairline-soft pb-space-sm text-title-md text-ink">Status Pesanan</h2>
                        <ol class="mt-space-md space-y-space-md">
                            <li v-for="(step, i) in steps" :key="step.label" class="flex items-start gap-space-md">
                                <span
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-caption text-white"
                                    :class="step.done ? 'bg-ink' : 'bg-field text-muted'"
                                >
                                    <span v-if="step.done" class="material-symbols-outlined text-[16px]">check</span>
                                    <span v-else>{{ i + 1 }}</span>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-body-md text-ink">{{ step.label }}</span>
                                    <span class="block text-caption text-muted">{{ step.hint }}</span>
                                </span>
                            </li>
                        </ol>
                    </section>

                    <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <div class="flex items-center justify-between border-b border-hairline-soft pb-space-sm">
                            <h2 class="text-title-md text-ink">Item</h2>
                            <span class="rounded-full bg-field px-2.5 py-1 text-badge-label text-ink">{{ order.items.length }} buku</span>
                        </div>
                        <div class="mt-space-md space-y-space-sm">
                            <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between gap-space-sm rounded-2xl bg-soft p-space-md">
                                <div class="min-w-0">
                                    <p class="truncate text-body-md text-ink">{{ item.book?.title || ('Buku #' + item.book_id) }}</p>
                                    <p class="text-caption text-muted">Harga dasar {{ rupiah(item.price) }}</p>
                                </div>
                                <span class="shrink-0 text-body-md text-ink">{{ rupiah(item.price) }}</span>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-5">
                    <div class="sticky top-24 space-y-space-md rounded-[24px] border border-hair bg-soft p-space-lg">
                        <h2 class="text-title-md text-ink">Ringkasan Biaya</h2>

                        <div class="space-y-space-sm text-body-sm">
                            <div class="flex justify-between text-muted">
                                <span>Subtotal</span>
                                <span class="text-ink">{{ rupiah(order.subtotal) }}</span>
                            </div>
                            <div v-if="Number(order.voucher_discount) > 0" class="flex justify-between text-ink">
                                <span>Voucher</span>
                                <span>−{{ rupiah(order.voucher_discount) }}</span>
                            </div>
                            <div v-if="Number(order.point_discount) > 0" class="flex justify-between text-ink">
                                <span>Poin ({{ order.points_used }} poin)</span>
                                <span>−{{ rupiah(order.point_discount) }}</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Biaya layanan</span>
                                <span class="text-ink">Gratis</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-hair pt-space-md">
                            <span class="text-title-md text-ink">Total</span>
                            <span class="text-title-md text-ink">{{ rupiah(order.total) }}</span>
                        </div>

                        <template v-if="order.status === 'pending'">
                            <button v-if="order.snap_token" type="button" class="btn-primary w-full gap-2" @click="payNow">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                Bayar Sekarang
                            </button>
                            <button v-else type="button" class="btn-primary w-full gap-2" @click="refreshToken">
                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                Muat Halaman Pembayaran
                            </button>
                            <p class="text-center text-caption text-muted">Tagihan kedaluwarsa otomatis 24 jam setelah pesanan dibuat.</p>
                        </template>

                        <Link v-if="order.status === 'paid'" :href="route('library.index')" class="btn-primary w-full gap-2">
                            <span class="material-symbols-outlined text-[18px]">auto_stories</span>
                            Buka Pustaka Saya
                        </Link>

                        <div class="flex items-start gap-space-sm rounded-xl bg-field p-space-sm text-caption text-muted">
                            <span class="material-symbols-outlined text-[16px] text-ink">support_agent</span>
                            <span>Ada kendala pembayaran? Hubungi tim bantuan dengan menyertakan ID pesanan ini.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
