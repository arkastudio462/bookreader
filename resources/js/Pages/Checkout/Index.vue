<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <!-- Ringkasan item -->
                <div class="card">
                    <h3 class="lk mb-4 text-ink">Buku yang Dibeli</h3>
                    <div v-for="item in items" :key="item.id" class="flex items-center justify-between border-b border-hairline-soft py-2 last:border-0">
                        <div>
                            <p class="lk text-ink">{{ item.book.title }}</p>
                            <p class="text-sm text-muted">{{ item.book.author_name }}</p>
                        </div>
                        <span class="lk text-ink">{{ rupiah(item.book.effective_price) }}</span>
                    </div>
                </div>

                <!-- Voucher & poin -->
                <div class="card">
                    <h3 class="lk mb-4 text-ink">Voucher &amp; Poin</h3>

                    <label class="lk block text-sm text-ink">Kode Voucher</label>
                    <input
                        v-model="form.voucher_code"
                        type="text"
                        placeholder="Mis. HEMAT20"
                        class="field mt-1 block w-full"
                    />
                    <p v-if="form.errors.voucher_code" class="lk mt-1 text-sm text-ink">{{ form.errors.voucher_code }}</p>
                    <p v-if="form.errors.cart" class="lk mt-1 text-sm text-ink">{{ form.errors.cart }}</p>

                    <label class="mt-4 flex items-center gap-2 text-sm text-muted">
                        <input
                            v-model="form.use_points"
                            type="checkbox"
                            class="rounded border-hair text-ink"
                            :disabled="max_redeemable <= 0"
                        />
                        <span>
                            Tukar poin ({{ points_balance }} tersedia, maks. {{ max_redeemable }} poin
                            = {{ rupiah(max_redeemable * redeem_rate) }})
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-faint">
                        1 poin = {{ rupiah(redeem_rate) }}, maksimal {{ max_redeem_percent }}% dari total.
                    </p>
                </div>

                <!-- Ringkasan biaya -->
                <div class="card">
                    <div class="flex justify-between py-1 text-muted">
                        <span>Subtotal</span>
                        <span class="lk text-ink">{{ rupiah(subtotal) }}</span>
                    </div>
                    <div v-if="pointDiscount > 0" class="flex justify-between py-1 text-ink">
                        <span>Potongan poin</span>
                        <span class="lk">−{{ rupiah(pointDiscount) }}</span>
                    </div>
                    <div class="mt-3 flex justify-between border-t border-hair pt-3">
                        <span class="h text-lg text-ink">Total Bayar</span>
                        <span class="h text-lg text-ink">{{ rupiah(total) }}</span>
                    </div>

                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="btn-primary mt-4 w-full disabled:opacity-50"
                    >
                        {{ form.processing ? 'Memproses…' : 'Buat Pesanan & Bayar' }}
                    </button>
                    <p class="mt-2 text-center text-xs text-faint">
                        Harga akhir dihitung ulang di server saat pesanan dibuat.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
