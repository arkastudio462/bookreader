<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    balance: Number,
    redeem_rate: Number,
    transactions: Array,
});

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');

const earned = computed(() =>
    props.transactions.filter((t) => t.points >= 0).reduce((sum, t) => sum + Number(t.points), 0),
);

const spent = computed(() =>
    Math.abs(props.transactions.filter((t) => t.points < 0).reduce((sum, t) => sum + Number(t.points), 0)),
);

const overview = computed(() => [
    { label: 'Saldo Poin', value: props.balance, icon: 'savings', sub: 'Siap dipakai saat checkout', tone: 'bg-ink text-white' },
    { label: 'Poin Didapat', value: earned.value, icon: 'trending_up', sub: 'Dari pembacaan & transaksi', tone: 'bg-canvas text-ink' },
    { label: 'Poin Digunakan', value: spent.value, icon: 'redeem', sub: 'Ditukar jadi potongan harga', tone: 'bg-canvas text-ink' },
    { label: 'Nilai Rupiah', value: rupiah(Number(props.balance ?? 0) * Number(props.redeem_rate ?? 0)), icon: 'payments', sub: `1 poin = ${rupiah(props.redeem_rate)}`, tone: 'bg-canvas text-ink' },
]);
</script>

<template>
    <Head title="Poin Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Poin Saya</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Akun</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Poin</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Poin PerpusKita</h1>
                    <p class="text-body-md text-muted">Kumpulkan poin dari setiap transaksi dan tukarkan menjadi potongan harga.</p>
                </div>
                <Link :href="route('catalog.index')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">redeem</span>
                    Tukar Poin
                </Link>
            </header>

            <section class="grid grid-cols-1 gap-space-md sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in overview"
                    :key="card.label"
                    class="flex flex-col justify-between rounded-[24px] border p-space-lg transition-colors"
                    :class="card.tone === 'bg-ink text-white' ? 'border-ink bg-ink text-white' : 'border-hair bg-canvas hover:border-ink'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-caption uppercase" :class="card.tone === 'bg-ink text-white' ? 'text-white/70' : 'text-muted'">{{ card.label }}</span>
                        <span class="material-symbols-outlined text-[20px]" :class="card.tone === 'bg-ink text-white' ? 'text-white' : 'text-accent'">{{ card.icon }}</span>
                    </div>
                    <div class="pt-space-md text-headline-sm font-semibold tracking-tight" :class="card.tone === 'bg-ink text-white' ? 'text-white' : 'text-ink'">
                        {{ typeof card.value === 'number' ? card.value.toLocaleString('id-ID') : card.value }}
                    </div>
                    <div class="mt-space-md border-t pt-space-sm text-caption" :class="card.tone === 'bg-ink text-white' ? 'border-white/20 text-white/70' : 'border-hairline-soft text-muted'">
                        {{ card.sub }}
                    </div>
                </article>
            </section>

            <section class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                <div class="flex flex-col justify-between gap-space-sm border-b border-hairline-soft pb-space-sm sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-title-md text-ink">Riwayat Poin</h2>
                        <p class="text-caption text-muted">Pergerakan poin pada akun Anda</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-soft px-2.5 py-1 text-badge-label text-ink">
                        <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                        {{ transactions.length }} transaksi
                    </span>
                </div>

                <div v-if="!transactions.length" class="py-space-xl text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                        <span class="material-symbols-outlined text-[28px] text-muted">history</span>
                    </div>
                    <p class="mt-space-md text-title-md text-ink">Belum ada transaksi poin</p>
                    <p class="mt-space-xs text-body-md text-muted">Poin akan otomatis masuk setiap kali Anda menyelesaikan pembelian.</p>
                    <Link :href="route('catalog.index')" class="btn-primary mt-space-lg gap-2 px-space-lg">
                        Belanja Sekarang
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </Link>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-hairline-soft text-muted text-caption uppercase">
                                <th class="px-space-md py-space-sm font-semibold">Tanggal</th>
                                <th class="px-space-md py-space-sm font-semibold">Keterangan</th>
                                <th class="px-space-md py-space-sm font-semibold">Tipe</th>
                                <th class="px-space-md py-space-sm text-right font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft text-body-sm text-ink">
                            <tr v-for="trx in transactions" :key="trx.id" class="transition-colors hover:bg-soft/60">
                                <td class="whitespace-nowrap px-space-md py-space-md text-muted">{{ trx.created_at }}</td>
                                <td class="px-space-md py-space-md">
                                    <span class="block font-medium text-ink">{{ trx.note || trx.type }}</span>
                                </td>
                                <td class="px-space-md py-space-md">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-badge-label"
                                          :class="trx.points >= 0 ? 'bg-emerald-50 text-emerald-900' : 'bg-soft text-muted'">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="trx.points >= 0 ? 'bg-emerald-600' : 'bg-faint'"></span>
                                        {{ trx.points >= 0 ? 'Didapat' : 'Digunakan' }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-md text-right font-semibold"
                                    :class="trx.points >= 0 ? 'text-ink' : 'text-muted'">
                                    {{ trx.points >= 0 ? '+' : '' }}{{ trx.points }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
