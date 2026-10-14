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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Checkout</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <!-- Ringkasan item -->
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 font-semibold text-gray-900">Buku yang Dibeli</h3>
                    <div v-for="item in items" :key="item.id" class="flex items-center justify-between border-b border-gray-100 py-2 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ item.book.title }}</p>
                            <p class="text-sm text-gray-600">{{ item.book.author_name }}</p>
                        </div>
                        <span class="text-gray-900">{{ rupiah(item.book.effective_price) }}</span>
                    </div>
                </div>

                <!-- Voucher & poin -->
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 font-semibold text-gray-900">Voucher &amp; Poin</h3>

                    <label class="block text-sm font-medium text-gray-700">Kode Voucher</label>
                    <input
                        v-model="form.voucher_code"
                        type="text"
                        placeholder="Mis. HEMAT20"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p v-if="form.errors.voucher_code" class="mt-1 text-sm text-red-600">{{ form.errors.voucher_code }}</p>
                    <p v-if="form.errors.cart" class="mt-1 text-sm text-red-600">{{ form.errors.cart }}</p>

                    <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.use_points"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600"
                            :disabled="max_redeemable <= 0"
                        />
                        <span>
                            Tukar poin ({{ points_balance }} tersedia, maks. {{ max_redeemable }} poin
                            = {{ rupiah(max_redeemable * redeem_rate) }})
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">
                        1 poin = {{ rupiah(redeem_rate) }}, maksimal {{ max_redeem_percent }}% dari total.
                    </p>
                </div>

                <!-- Ringkasan biaya -->
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex justify-between py-1 text-gray-700">
                        <span>Subtotal</span>
                        <span>{{ rupiah(subtotal) }}</span>
                    </div>
                    <div v-if="pointDiscount > 0" class="flex justify-between py-1 text-green-700">
                        <span>Potongan poin</span>
                        <span>−{{ rupiah(pointDiscount) }}</span>
                    </div>
                    <div class="mt-3 flex justify-between border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                        <span>Total Bayar</span>
                        <span>{{ rupiah(total) }}</span>
                    </div>

                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Memproses…' : 'Buat Pesanan & Bayar' }}
                    </button>
                    <p class="mt-2 text-center text-xs text-gray-500">
                        Harga akhir dihitung ulang di server saat pesanan dibuat.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
