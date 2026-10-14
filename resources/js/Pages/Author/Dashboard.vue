<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    stats: { type: Object, default: () => ({}) },
});

const activePeriod = ref('30');
const activeCatalogTab = ref('all');
const discountPercent = ref('20');

const kpis = [
    { label: 'Total Royalti Akumulatif', value: 'Rp 38.450.000', sub: 'Bagi hasil bersih 70% otomatis tercatat', badge: '+14.8% bln ini', icon: 'trending_up', badgeColor: 'text-accent' },
    { label: 'Saldo Tersedia (Payout)', value: 'Rp 12.820.000', sub: 'BCA •••• 9812', badge: 'Siap Tarik', badgeColor: 'bg-accent text-white' },
    { label: 'Buku & Lisensi Terjual', value: '4.890', unit: 'exp', sub: '3.420 EPUB 3 • 1.470 PDF Interaktif', icon: 'auto_stories' },
    { label: 'Kepuasan & Pembaca', value: '38.400+', sub: 'Dari 1.840 ulasan pembaca terverifikasi', rating: '4.92 / 5.0' },
];

const salesBars = [35, 42, 30, 55, 68, 48, 60, 74, 62, 45, 94, 80, 58, 65, 70, 52, 82, 66, 75, 88, 72, 60];
const peakIndex = 10;

const catalogBooks = [
    { title: 'Hujan di Atas Kota Lama', category: 'Fiksi Sejarah', format: 'EPUB 3 + PDF HD', isbn: '978-623-09-1120-4', status: 'Best Seller', statusColor: 'bg-accent', sold: '2.450', price: 'Rp 49.000', royalty: 'Rp 17.150.000', rating: '4.9', reviews: '1.120' },
    { title: 'Kopi, Kota, dan Kamu', category: 'Esai & Antologi Urban', format: 'EPUB 3', isbn: '978-623-09-2341-9', status: 'Aktif', statusColor: 'bg-ink', sold: '1.280', price: 'Rp 45.000', royalty: 'Rp 8.064.000', rating: '4.8', reviews: '490' },
    { title: 'Arsitektur Batavia Tempo Dulu', category: 'Sejarah & Desain', format: 'PDF HD', isbn: '978-623-09-5510-2', status: 'Aktif', statusColor: 'bg-ink', sold: '840', price: 'Rp 68.000', royalty: 'Rp 7.996.000', rating: '4.8', reviews: '188' },
    { title: 'Senja di Pelabuhan Ratu', category: 'Romansa Pesisir', format: 'EPUB 3', isbn: '978-623-09-9801-0', status: 'Akses Terbuka', statusColor: 'bg-field', sold: '320', price: 'Gratis', royalty: 'Karya Komunitas', rating: '5.0', reviews: '42' },
];

const transactions = [
    { initials: 'DR', bookTitle: 'Hujan di Atas Kota Lama', buyer: 'Dimas R.', method: 'QRIS BCA', time: '4 menit lalu', amount: '+ Rp 34.300', id: '#TRX-88219' },
    { initials: 'AN', bookTitle: 'Kopi, Kota, dan Kamu', buyer: 'Anisa N.', method: 'GoPay', time: '28 menit lalu', amount: '+ Rp 31.500', id: '#TRX-88218' },
    { initials: 'TA', bookTitle: 'Arsitektur Batavia Tempo Dulu', buyer: 'Taufik A.', method: 'Saldo PerpusKita', time: '1 jam lalu', amount: '+ Rp 47.600', id: '#TRX-88217' },
    { initials: 'MW', bookTitle: 'Hujan di Atas Kota Lama', buyer: 'Maya W.', method: 'Kartu Debit', time: '3 jam lalu', amount: '+ Rp 34.300', id: '#TRX-88216' },
];
</script>

<template>
    <Head title="Dashboard Penulis" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Dasbor Penulis</h2>
        </template>

        <div class="mx-auto w-full max-w-[1440px] space-y-12 px-4 py-8 sm:px-8 lg:px-12">
            <header class="flex flex-col justify-between gap-6 pb-8 lg:flex-row lg:items-end">
                <div class="flex max-w-3xl flex-col gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-field px-3 py-1 text-badge-label text-ink">
                            <span class="material-symbols-outlined text-[16px] text-accent" style="font-variation-settings: 'FILL' 1;">verified</span>
                            Penulis Terverifikasi
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-field px-3 py-1 text-badge-label text-ink">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                            Mitra Royalti Aktif (70:30)
                        </span>
                        <span class="text-muted text-caption">ID Mitra: #AUT-88204-ID</span>
                    </div>
                    <div class="flex flex-wrap items-baseline gap-3">
                        <h1 class="text-headline-lg tracking-tight text-ink">Arya Wirawan</h1>
                        <span class="text-title-md text-muted">@aryawirawan</span>
                    </div>
                    <p class="max-w-2xl text-body-lead text-muted">Pantau performa penjualan karya, transparansi royalti harian, dan kelola penerbitan naskah digitalmu secara terpusat.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" class="flex items-center gap-2 rounded-full bg-field px-5 py-3 text-action text-ink transition-colors hover:bg-soft">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                        Kelola Rekening Bank
                    </button>
                    <Link :href="route('author.books.create')" class="flex items-center gap-2 rounded-full bg-ink px-6 py-3 text-action text-white transition-opacity hover:opacity-90">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Unggah Naskah Baru
                    </Link>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="kpi in kpis" :key="kpi.label" class="flex flex-col justify-between gap-4 rounded-lg bg-soft p-6">
                    <div class="flex items-center justify-between">
                        <span class="text-caption tracking-wider text-muted uppercase">{{ kpi.label }}</span>
                        <span v-if="kpi.badge" class="inline-flex items-center gap-1 rounded-full bg-canvas px-2.5 py-0.5 text-badge-label" :class="kpi.badgeColor">
                            <span v-if="kpi.icon" class="material-symbols-outlined text-[14px]">{{ kpi.icon }}</span>
                            {{ kpi.badge }}
                        </span>
                        <span v-else-if="kpi.icon" class="material-symbols-outlined text-muted text-[20px]">{{ kpi.icon }}</span>
                    </div>
                    <div>
                        <div class="text-headline-sm font-semibold tracking-tight text-ink">{{ kpi.value }} <span v-if="kpi.unit" class="text-body-md font-normal text-muted">{{ kpi.unit }}</span></div>
                        <p class="mt-1.5 flex items-center gap-1 text-caption text-muted">
                            <span v-if="kpi.sub && kpi.sub.includes('70%')" class="material-symbols-outlined text-[14px]">verified_user</span>
                            {{ kpi.sub }}
                        </p>
                        <div v-if="kpi.rating" class="mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-accent" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="text-badge-label text-ink">{{ kpi.rating }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg bg-soft p-6 sm:p-8">
                <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <h2 class="text-title-lg tracking-tight text-ink">Tren Royalti & Penjualan Harian</h2>
                        <p class="mt-0.5 text-body-sm text-muted">Pantauan volume pembelian karya dan royalti masuk 30 hari ke belakang.</p>
                    </div>
                    <div class="inline-flex self-start rounded-full bg-field p-1 text-ink md:self-auto">
                        <button v-for="p in ['7', '30', '90', 'year']" :key="p" type="button" class="rounded-full px-4 py-1.5 text-action text-sm transition-colors" :class="activePeriod === p ? 'bg-canvas text-ink' : 'text-muted hover:text-ink'" @click="activePeriod = p">
                            {{ p === 'year' ? 'Tahun Ini' : p + ' Hari' }}
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 items-end gap-8 lg:grid-cols-12">
                    <div class="flex flex-col gap-4 lg:col-span-8">
                        <div class="relative flex h-64 items-end justify-between gap-1.5 px-2 pb-6 sm:gap-2">
                            <div class="pointer-events-none absolute inset-0 flex flex-col justify-between opacity-30 pb-6">
                                <div class="h-px w-full bg-faint"></div>
                                <div class="h-px w-full bg-faint"></div>
                                <div class="h-px w-full bg-faint"></div>
                                <div class="h-px w-full bg-faint"></div>
                            </div>
                            <div
                                v-for="(height, i) in salesBars"
                                :key="i"
                                class="group relative flex flex-1 flex-col items-center justify-end rounded-t transition-all"
                                :class="i === peakIndex ? 'bg-ink' : 'bg-field hover:bg-ink'"
                                :style="{ height: height + '%' }"
                            >
                                <span v-if="i === peakIndex" class="pointer-events-none absolute -top-12 z-10 flex items-center gap-1.5 rounded-full bg-ink px-3 py-1.5 text-caption whitespace-nowrap text-white shadow-md">
                                    <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                                    12 Nov: Rp 1.850.000 (142 exp)
                                </span>
                                <span v-else-if="i % 3 === 0" class="pointer-events-none absolute -top-8 rounded bg-ink px-2 py-0.5 text-[11px] whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100">Rp {{ (height * 12).toLocaleString('id-ID') }}rb</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-2 text-muted text-caption">
                            <span>24 Okt</span>
                            <span>01 Nov</span>
                            <span class="font-semibold text-ink">12 Nov (Puncak)</span>
                            <span>20 Nov</span>
                            <span>Hari Ini</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 rounded-lg bg-canvas p-5 lg:col-span-4">
                        <div class="flex flex-col gap-1 pb-4">
                            <span class="text-caption tracking-wider text-muted uppercase">Rata-rata Penjualan</span>
                            <div class="text-title-lg font-semibold text-ink">Rp 1.280.000 <span class="text-body-sm font-normal text-muted">/ hari</span></div>
                            <span class="text-caption text-muted">Konsisten di 28 transaksi harian</span>
                        </div>
                        <div class="flex flex-col gap-1 pb-4">
                            <span class="text-caption tracking-wider text-muted uppercase">Konversi Sampel Gratis</span>
                            <div class="flex items-center gap-2">
                                <span class="text-title-lg font-semibold text-ink">64.2%</span>
                                <span class="rounded-full bg-field px-2 py-0.5 text-badge-label text-ink">Tinggi</span>
                            </div>
                            <span class="text-caption text-muted">Pembaca Bab 1-3 lanjut beli penuh</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-caption tracking-wider text-muted uppercase">Hari Berpenghasilan Terbaik</span>
                            <span class="text-body-md font-medium text-ink">Minggu & Tanggal Gajian (25-28)</span>
                            <span class="text-caption text-muted">Meningkat +38% dibanding hari kerja biasa</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="space-y-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-title-lg tracking-tight text-ink">Katalog Karya Digital</h2>
                        <p class="mt-0.5 text-body-sm text-muted">Kelola lisensi edisi digital, berkas naskah, dan pengaturan harga.</p>
                    </div>
                    <div class="inline-flex max-w-full overflow-x-auto rounded-full bg-field p-1 text-ink">
                        <button
                            v-for="tab in [{ id: 'all', label: 'Semua Buku (4)' }, { id: 'review', label: 'Dalam Peninjauan (1)' }, { id: 'draft', label: 'Draf Naskah (2)' }, { id: 'partner', label: 'Bagi Hasil Mitra (1)' }]"
                            :key="tab.id"
                            type="button"
                            class="shrink-0 rounded-full px-4 py-2 text-action text-sm whitespace-nowrap transition-colors"
                            :class="activeCatalogTab === tab.id ? 'bg-canvas text-ink' : 'text-muted hover:text-ink'"
                            @click="activeCatalogTab = tab.id"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-soft">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="text-muted text-caption tracking-wider uppercase">
                                    <th class="px-6 py-4 font-semibold">Karya & Format</th>
                                    <th class="px-4 py-4 font-semibold">Status</th>
                                    <th class="px-4 py-4 font-semibold">Terjual</th>
                                    <th class="px-4 py-4 font-semibold">Harga</th>
                                    <th class="px-4 py-4 font-semibold">Royalti (70%)</th>
                                    <th class="px-4 py-4 font-semibold">Rating</th>
                                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-field bg-canvas text-body-sm">
                                <tr v-for="book in catalogBooks" :key="book.isbn" class="transition-colors hover:bg-soft/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="h-16 w-12 flex-shrink-0 rounded bg-field"></div>
                                            <div class="flex min-w-0 flex-col">
                                                <span class="text-action truncate text-ink">{{ book.title }}</span>
                                                <span class="text-caption text-muted">{{ book.category }} • {{ book.format }}</span>
                                                <span class="text-caption text-faint">ISBN: {{ book.isbn }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-badge-label" :class="book.status === 'Best Seller' ? 'bg-accent text-white' : 'bg-field text-ink'">
                                            <span class="h-2 w-2 rounded-full" :class="book.status === 'Best Seller' ? 'bg-white' : 'bg-ink'"></span>
                                            {{ book.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-medium whitespace-nowrap text-ink">{{ book.sold }} <span class="text-xs font-normal text-muted">exp</span></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-ink">{{ book.price }}</td>
                                    <td class="px-4 py-4 font-semibold whitespace-nowrap text-ink">{{ book.royalty }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1 font-medium text-ink">
                                            <span class="material-symbols-outlined text-[16px] text-accent" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span>{{ book.rating }}</span>
                                            <span class="text-xs text-muted">({{ book.reviews }})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <Link :href="route('author.books.index')" class="rounded-full bg-field px-3 py-1.5 text-action text-xs text-ink transition-colors hover:bg-soft">Edit</Link>
                                            <button type="button" class="rounded-full bg-ink px-3 py-1.5 text-action text-xs text-white transition-opacity hover:opacity-90">Laporan</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <section class="space-y-6 rounded-lg bg-soft p-6 sm:p-8 lg:col-span-7">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-title-lg tracking-tight text-ink">Ledger Transaksi Real-time</h2>
                                <span class="h-2 w-2 animate-ping rounded-full bg-accent"></span>
                            </div>
                            <p class="mt-0.5 text-body-sm text-muted">Setiap penjualan langsung masuk ke saldo tanpa potongan tersembunyi.</p>
                        </div>
                        <button type="button" class="flex items-center gap-1.5 self-start rounded-full bg-canvas px-4 py-2 text-action text-xs text-ink transition-colors hover:bg-field sm:self-auto">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Ekspor CSV
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div v-for="trx in transactions" :key="trx.id" class="flex items-center justify-between gap-4 rounded-lg bg-canvas p-4 transition-colors hover:bg-canvas/90">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="grid h-8 w-8 flex-shrink-0 place-items-center rounded-full bg-field text-xs font-medium text-ink">{{ trx.initials }}</div>
                                <div class="flex min-w-0 flex-col">
                                    <span class="text-action truncate text-sm text-ink">{{ trx.bookTitle }}</span>
                                    <span class="text-caption text-muted">{{ trx.buyer }} • {{ trx.method }} • {{ trx.time }}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span class="text-action text-sm text-accent">{{ trx.amount }}</span>
                                <span class="block text-caption text-faint">{{ trx.id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 text-muted text-caption">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-accent">verified</span>
                            Bagi hasil: 70% Penulis, 30% Platform
                        </span>
                        <a href="#" class="font-medium text-ink transition-colors hover:underline">Buku Besar</a>
                    </div>
                </section>

                <section class="space-y-6 rounded-lg bg-soft p-6 sm:p-8 lg:col-span-5">
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="text-title-lg tracking-tight text-ink">Flash Sale Mandiri</h2>
                            <span class="rounded-full bg-field px-2.5 py-0.5 text-badge-label text-ink">Eksklusif</span>
                        </div>
                        <p class="mt-0.5 text-body-sm text-muted">Dorong pembaca baru dengan program potongan harga.</p>
                    </div>
                    <form class="space-y-4" @submit.prevent>
                        <div class="space-y-1.5">
                            <label class="text-caption tracking-wider text-muted uppercase">Pilih Judul Buku</label>
                            <select class="h-12 w-full cursor-pointer appearance-none rounded bg-field px-4 text-body-md text-ink focus:outline-none focus:ring-2 focus:ring-ink">
                                <option>Hujan di Atas Kota Lama (Rp 49.000)</option>
                                <option>Kopi, Kota, dan Kamu (Rp 45.000)</option>
                                <option>Arsitektur Batavia Tempo Dulu (Rp 68.000)</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-caption tracking-wider text-muted uppercase">Persentase Diskon</label>
                            <div class="grid grid-cols-4 gap-2">
                                <label v-for="pct in ['10', '20', '30', '40']" :key="pct" class="cursor-pointer">
                                    <input v-model="discountPercent" type="radio" name="discount" :value="pct" class="peer sr-only" />
                                    <div class="flex h-10 items-center justify-center rounded-full bg-field text-action text-xs text-ink transition-colors peer-checked:bg-ink peer-checked:text-white">{{ pct }}%</div>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-caption tracking-wider text-muted uppercase">Masa Berlaku Promo</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex h-12 items-center justify-between rounded bg-field px-4 text-sm text-ink"><span class="text-muted">Mulai</span><span class="font-medium">Hari Ini</span></div>
                                <div class="flex h-12 items-center justify-between rounded bg-field px-4 text-sm text-ink"><span class="text-muted">Durasi</span><span class="font-medium">48 Jam</span></div>
                            </div>
                        </div>
                        <div class="space-y-2 rounded-lg bg-canvas p-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-muted">Harga Pasca Diskon:</span>
                                <span class="font-medium text-ink line-through mr-1">Rp 49.000</span>
                                <span class="font-semibold text-ink">Rp {{ (49000 * (1 - discountPercent / 100)).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-muted">Estimasi Royalti (70%):</span>
                                <span class="font-semibold text-accent">Rp {{ ((49000 * (1 - discountPercent / 100)) * 0.7).toLocaleString('id-ID') }} / penjualan</span>
                            </div>
                        </div>
                        <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-full bg-ink text-action text-white transition-opacity hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">bolt</span>
                            Aktifkan Kampanye Diskon
                        </button>
                    </form>
                </section>
            </div>

            <div class="flex flex-col items-start justify-between gap-4 rounded-lg bg-field p-6 md:flex-row md:items-center">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-full bg-canvas text-ink">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                    </div>
                    <div>
                        <span class="block text-action text-sm text-ink">Proteksi Hak Cipta & Lisensi Digital Dilindungi</span>
                        <span class="text-caption text-muted">Setiap unduhan EPUB/PDF dilindungi dynamic digital watermark.</span>
                    </div>
                </div>
                <a href="#" class="flex-shrink-0 text-caption font-semibold text-ink transition-colors hover:underline">Panduan Royalti</a>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
