<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CoverImage from '@/Components/CoverImage.vue';

const props = defineProps({
    items: Array,
    subtotal: Number,
    points_balance: Number,
    max_redeemable: Number,
    redeem_rate: Number,
    max_redeem_percent: Number,
});

const form = useForm({
    voucher_code: '',
    use_points: false,
});

const paymentMethod = ref('midtrans');

const paymentMethods = [
    { id: 'midtrans', label: 'Midtrans', desc: 'QRIS, GoPay, VA, kartu kredit & debit', icon: 'account_balance_wallet' },
    { id: 'balance', label: 'Saldo PerpusKita', desc: 'Bayar langsung dari saldo akun', icon: 'savings' },
];

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');

const pointDiscount = computed(() =>
    form.use_points ? Number(props.max_redeemable) * Number(props.redeem_rate) : 0,
);

const total = computed(() => Math.max(0, Number(props.subtotal) - pointDiscount.value));

function submit() {
    form.post(route('checkout.store'));
}
</script>

<template>
    <Head title="Checkout - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Checkout</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="border-b border-hair pb-space-lg">
                <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                    <Link :href="route('cart.index')" class="transition-colors hover:text-ink">Keranjang</Link>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="font-semibold text-ink">Checkout</span>
                </nav>
                <h1 class="mt-space-xs text-headline-sm tracking-tight text-ink">Selesaikan Pembelian</h1>
                <p class="mt-space-xs text-body-md text-muted">Periksa kembali daftar judul dan metode pembayaran Anda.</p>
            </header>

            <div class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <div class="space-y-space-lg lg:col-span-7">
                    <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <div class="flex items-center justify-between border-b border-hairline-soft pb-space-sm">
                            <h2 class="text-title-md text-ink">Buku yang Dibeli</h2>
                            <span class="rounded-full bg-field px-2.5 py-1 text-badge-label text-ink">{{ items.length }} judul</span>
                        </div>
                        <div class="mt-space-md space-y-space-sm">
                            <div
                                v-for="item in items"
                                :key="item.id"
                                class="flex items-center gap-space-md rounded-2xl bg-soft p-space-sm"
                            >
                                <div class="w-12 shrink-0 overflow-hidden rounded-sm bg-canvas">
                                    <div class="aspect-[3/4]">
                                        <img
                                            v-if="item.book.cover_url"
                                            :src="item.book.cover_url"
                                            :alt="item.book.title"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                        <CoverImage v-else :book="item.book" />
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-body-md text-ink">{{ item.book.title }}</p>
                                    <p class="truncate text-caption text-muted">{{ item.book.author_name }}</p>
                                </div>
                                <span class="shrink-0 text-body-md text-ink">{{ rupiah(item.book.effective_price) }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <div class="border-b border-hairline-soft pb-space-sm">
                            <h2 class="text-title-md text-ink">Voucher &amp; Poin</h2>
                        </div>

                        <div class="mt-space-md space-y-space-md">
                            <div>
                                <label for="voucher" class="text-body-sm text-ink">Kode Voucher</label>
                                <div class="mt-1 flex gap-space-sm">
                                    <input
                                        id="voucher"
                                        v-model="form.voucher_code"
                                        type="text"
                                        placeholder="Mis. HEMAT20"
                                        class="field min-w-0 flex-1"
                                    />
                                    <button type="button" class="btn-soft shrink-0 px-space-md text-sm">Terapkan</button>
                                </div>
                                <p v-if="form.errors.voucher_code" class="mt-space-xs text-caption text-error">{{ form.errors.voucher_code }}</p>
                                <p v-if="form.errors.cart" class="mt-space-xs text-caption text-error">{{ form.errors.cart }}</p>
                            </div>

                            <div class="rounded-2xl bg-soft p-space-md">
                                <label class="flex items-start gap-space-sm cursor-pointer">
                                    <input
                                        v-model="form.use_points"
                                        type="checkbox"
                                        class="mt-1 h-4 w-4 rounded border-hair text-ink"
                                        :disabled="max_redeemable <= 0"
                                    />
                                    <span class="min-w-0">
                                        <span class="block text-body-md text-ink">Tukar poin saya</span>
                                        <span class="block text-caption text-muted">
                                            {{ points_balance }} poin tersedia · maks. {{ max_redeemable }} poin =
                                            {{ rupiah(max_redeemable * redeem_rate) }}
                                        </span>
                                    </span>
                                </label>
                                <p class="mt-space-sm text-caption text-muted">
                                    1 poin = {{ rupiah(redeem_rate) }}, maksimal {{ max_redeem_percent }}% dari total belanja.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <div class="border-b border-hairline-soft pb-space-sm">
                            <h2 class="text-title-md text-ink">Metode Pembayaran</h2>
                        </div>
                        <div class="mt-space-md grid grid-cols-1 gap-space-sm sm:grid-cols-2">
                            <label
                                v-for="method in paymentMethods"
                                :key="method.id"
                                class="flex cursor-pointer items-start gap-space-sm rounded-2xl border p-space-md transition-colors"
                                :class="paymentMethod === method.id ? 'border-ink bg-soft' : 'border-hair bg-canvas hover:bg-soft'"
                            >
                                <input v-model="paymentMethod" type="radio" :value="method.id" class="mt-1 accent-ink" />
                                <span class="min-w-0">
                                    <span class="flex items-center gap-1 text-action text-ink">
                                        <span class="material-symbols-outlined text-[18px]">{{ method.icon }}</span>
                                        {{ method.label }}
                                    </span>
                                    <span class="mt-0.5 block text-caption text-muted">{{ method.desc }}</span>
                                </span>
                            </label>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-5">
                    <div class="sticky top-24 space-y-space-md rounded-[24px] border border-hair bg-soft p-space-lg">
                        <h2 class="text-title-md text-ink">Ringkasan Pembayaran</h2>

                        <div class="space-y-space-sm text-body-sm">
                            <div class="flex justify-between text-muted">
                                <span>Subtotal</span>
                                <span class="text-ink">{{ rupiah(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Voucher</span>
                                <span class="text-ink">Belum dipakai</span>
                            </div>
                            <div v-if="pointDiscount > 0" class="flex justify-between text-ink">
                                <span>Potongan poin</span>
                                <span>−{{ rupiah(pointDiscount) }}</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Biaya layanan</span>
                                <span class="text-ink">Gratis</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-hair pt-space-md">
                            <span class="text-title-md text-ink">Total Bayar</span>
                            <span class="text-title-md text-ink">{{ rupiah(total) }}</span>
                        </div>

                        <button
                            type="button"
                            class="btn-primary w-full gap-2 disabled:opacity-50"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            <span v-if="!form.processing" class="material-symbols-outlined text-[18px]">lock</span>
                            {{ form.processing ? 'Memproses…' : 'Buat Pesanan & Bayar' }}
                        </button>

                        <p class="text-center text-caption text-muted">
                            Harga akhir dihitung ulang di server saat pesanan dibuat.
                        </p>

                        <div class="flex items-start gap-space-sm rounded-xl bg-field p-space-sm text-caption text-muted">
                            <span class="material-symbols-outlined text-[16px] text-ink">verified_user</span>
                            <span>Transaksi Anda diamankan dengan enkripsi TLS dan perlindungan hak cipta DRM.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
