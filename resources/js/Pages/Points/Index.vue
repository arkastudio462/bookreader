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
            <h2 class="h text-xl">Poin Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div class="card-soft p-6">
                    <p class="lt text-sm text-muted">Saldo Poin</p>
                    <p class="h text-4xl">{{ balance }}</p>
                    <p class="lt mt-1 text-sm text-muted">1 poin = {{ rupiah(redeem_rate) }} saat checkout</p>
                </div>

                <div class="card p-6">
                    <h3 class="lk mb-4 text-ink">Riwayat Poin</h3>
                    <div v-if="!transactions.length" class="py-6 text-center text-sm text-muted">
                        Belum ada transaksi poin.
                    </div>
                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-hair text-left text-muted">
                                <th class="py-2">Tanggal</th>
                                <th class="py-2">Keterangan</th>
                                <th class="py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="trx in transactions" :key="trx.id" class="border-b border-hairline-soft last:border-0">
                                <td class="py-2 text-muted">{{ trx.created_at }}</td>
                                <td class="py-2 text-ink">{{ trx.note || trx.type }}</td>
                                <td
                                    class="lk py-2 text-right"
                                    :class="trx.points >= 0 ? 'text-ink' : 'text-muted'"
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
