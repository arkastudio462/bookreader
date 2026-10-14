<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    balance: Number,
    redeem_rate: Number,
    transactions: Array,
});

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');
</script>

<template>
    <Head title="Poin Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Poin Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white shadow">
                    <p class="text-sm opacity-90">Saldo Poin</p>
                    <p class="text-4xl font-bold">{{ balance }}</p>
                    <p class="mt-1 text-sm opacity-90">1 poin = {{ rupiah(redeem_rate) }} saat checkout</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 font-semibold text-gray-900">Riwayat Poin</h3>
                    <div v-if="!transactions.length" class="py-6 text-center text-sm text-gray-500">
                        Belum ada transaksi poin.
                    </div>
                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-600">
                                <th class="py-2">Tanggal</th>
                                <th class="py-2">Keterangan</th>
                                <th class="py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="trx in transactions" :key="trx.id" class="border-b border-gray-100 last:border-0">
                                <td class="py-2 text-gray-600">{{ trx.created_at }}</td>
                                <td class="py-2 text-gray-900">{{ trx.note || trx.type }}</td>
                                <td
                                    class="py-2 text-right font-semibold"
                                    :class="trx.points >= 0 ? 'text-green-600' : 'text-red-600'"
                                >
                                    {{ trx.points >= 0 ? '+' : '' }}{{ trx.points }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
