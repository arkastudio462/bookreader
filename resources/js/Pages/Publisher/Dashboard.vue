<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    stats: { type: Object, default: () => ({}) },
});

const activePeriod = ref('30');
const activeImprint = ref('all');

const kpis = [
    { label: 'Penjualan Bruto Katalog', value: 'Rp 184.250.000', badge: '+22.4%', sub: 'Bagi hasil bersih penerbit: Rp 55.275.000 (30%)', icon: 'payments' },
    { label: 'Royalti Penulis Terbayar', value: 'Rp 128.975.000', sub: 'Dialirkan ke 18 penulis terikat kontrak', badge: 'Escrow' },
    { label: 'Total Eksemplar Terjual', value: '14.820', unit: 'eks', sub: '11.200 EPUB 3 • 3.620 PDF Interaktif', icon: 'auto_stories' },
    { label: 'Naskah dalam Proses', value: '6 Judul Aktif', badge: 'SLA 2.8 Hari', sub: 'Kurasi metadata, ISBN & DRM LCP', badgeColor: 'bg-field text-ink' },
];

const salesBars = [48, 62, 78, 168, 96, 70, 84, 110, 90, 125];
const peakIndex = 3;

const imprints = [
    { name: 'Matapena Sastra', pct: 54, amount: 'Rp 99.4M', color: 'bg-ink' },
    { name: 'Lentera Populer', pct: 28, amount: 'Rp 51.5M', color: 'bg-accent' },
    { name: 'Akademia & Riset', pct: 18, amount: 'Rp 33.1M', color: 'bg-muted' },
];

const catalogBooks = [
    { title: 'Hujan di Atas Kota Lama', author: 'Arya Wirawan', isbn: '978-623-09-1120-4', imprint: 'Matapena Sastra', sold: '3.450', price: 'Rp 49.000', gross: 'Rp 169.050.000', status: 'Bestseller', statusColor: 'bg-accent text-white' },
    { title: 'Kopi, Kota, dan Kamu', author: 'Bima Wicaksono & Dian S.', isbn: '978-623-09-2341-9', imprint: 'Lentera Populer', sold: '1.820', price: 'Rp 45.000', gross: 'Rp 81.900.000', status: 'Aktif', statusColor: 'bg-canvas text-ink' },
    { title: 'Bunga Api di Lembah Merapi', author: 'Dian Sastrowardoyo', isbn: '978-623-01-2900-1', imprint: 'Matapena Sastra', sold: '890', price: 'Rp 55.000', gross: 'Rp 48.950.000', status: 'Review ISBN', statusColor: 'bg-field text-muted' },
    { title: 'Arsitektur Batavia Tempo Dulu', author: 'Tim Riset Sejarah', isbn: '978-623-09-5510-2', imprint: 'Akademia & Riset', sold: '640', price: 'Rp 68.000', gross: 'Rp 43.520.000', status: 'Aktif', statusColor: 'bg-canvas text-ink' },
];

const authorPayouts = [
    { initials: 'AW', name: 'Arya Wirawan', contract: '70% Penulis / 30% Matapena', amount: 'Rp 24.150.000', status: 'Terjadwal 15 Feb', statusColor: 'text-accent' },
    { initials: 'DS', name: 'Dian Sastrowardoyo', contract: '65% Penulis / 35% Matapena', amount: 'Rp 16.800.000', status: 'Terjadwal 15 Feb', statusColor: 'text-accent' },
    { initials: 'BW', name: 'Bima Wicaksono', contract: '70% Penulis / 30% Matapena', amount: 'Rp 9.450.000', status: 'Terverifikasi', statusColor: 'text-ink' },
];

const submissions = [
    { title: 'Sengketa Tak Bernama', format: 'EPUB 3 + Audio Narasi', author: 'Reza Andana', progress: 75, status: 'Verifikasi DRM LCP Selesai', statusIcon: 'check_circle', statusColor: 'text-accent' },
    { title: 'Catatan Pinggir Pesisir', format: 'PDF Interaktif', author: 'Ratna Ayu', progress: 40, status: 'Uji Tata Letak Reader', statusIcon: 'hourglass_top', statusColor: 'text-faint' },
];
</script>

<template>
    <Head title="Dashboard Penerbit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Dasbor Penerbit</h2>
        </template>

        <div class="mx-auto w-full max-w-[1440px] space-y-12 px-4 py-8 sm:px-8 lg:px-12">
            <header class="flex flex-col gap-space-md">
                <div class="flex items-center gap-space-xs text-caption text-muted">
                    <span class="cursor-pointer transition-colors hover:text-ink">Studio Penerbit</span>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-ink">Ringkasan & Performa Katalog</span>
                </div>
                <div class="flex flex-col justify-between gap-space-lg lg:flex-row lg:items-end">
                    <div class="flex max-w-2xl flex-col gap-space-xs">
                        <div class="inline-flex items-center gap-space-xs">
                            <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-accent"></span>
                            <span class="text-caption tracking-wider font-semibold text-accent uppercase">Konsol Penerbit Aktif</span>
                            <span class="text-faint text-caption">•</span>
                            <span class="text-caption text-muted">ID: ID-PUB-8810 (Matapena Press PT)</span>
                        </div>
                        <h1 class="text-headline-sm font-semibold tracking-tight text-ink">Dasbor Operasional Penerbit — Matapena Press</h1>
                        <p class="text-body-md text-muted">Kelola 48 judul terbitan, pantau sirkulasi royalti 18 penulis mitra, dan percepat persetujuan naskah digital.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-space-sm">
                        <div class="flex items-center rounded-full bg-field p-1">
                            <button
                                v-for="p in ['30', 'month', 'q1', 'year']"
                                :key="p"
                                type="button"
                                class="rounded-full px-space-md py-1.5 text-action text-body-sm transition-all"
                                :class="activePeriod === p ? 'bg-canvas text-ink shadow-sm' : 'text-muted hover:text-ink'"
                                @click="activePeriod = p"
                            >
                                {{ p === '30' ? '30 Hari' : p === 'month' ? 'Bulan Ini' : p === 'q1' ? 'Q1 2025' : 'Tahun Ini' }}
                            </button>
                        </div>
                        <button type="button" class="inline-flex h-10 items-center gap-space-xs rounded-full bg-soft px-space-md text-body-sm text-action text-ink transition-colors hover:bg-hair">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            <span>Unduh Laporan</span>
                        </button>
                        <button type="button" class="inline-flex h-10 items-center gap-space-xs rounded-full bg-ink px-space-lg text-body-sm text-action text-white transition-opacity hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            <span>Judul Baru</span>
                        </button>
                    </div>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-space-md md:grid-cols-2 lg:grid-cols-4">
                <div v-for="kpi in kpis" :key="kpi.label" class="flex flex-col justify-between gap-space-md rounded-lg bg-soft p-space-lg transition-colors hover:bg-field">
                    <div class="flex items-start justify-between">
                        <span class="text-caption tracking-wider text-muted uppercase">{{ kpi.label }}</span>
                        <span v-if="kpi.icon" class="grid h-8 w-8 place-items-center rounded-full bg-canvas text-accent">
                            <span class="material-symbols-outlined text-[18px]">{{ kpi.icon }}</span>
                        </span>
                        <span v-else-if="kpi.badge" class="rounded-full px-2.5 py-0.5 text-badge-label" :class="kpi.badgeColor || 'bg-accent text-white'">
                            {{ kpi.badge }}
                        </span>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-space-xs">
                            <span class="text-title-lg font-semibold tracking-tight text-ink">{{ kpi.value }}</span>
                            <span v-if="kpi.badge === '+22.4%'" class="text-accent text-badge-label font-semibold">{{ kpi.badge }}</span>
                            <span v-if="kpi.unit" class="text-caption text-muted">{{ kpi.unit }}</span>
                        </div>
                        <p class="mt-1 text-caption text-muted">{{ kpi.sub }}</p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <div class="flex flex-col justify-between gap-space-lg rounded-lg bg-soft p-space-lg lg:col-span-8">
                    <div class="flex flex-col justify-between gap-space-sm md:flex-row md:items-center">
                        <div>
                            <span class="text-badge-label tracking-wider text-muted uppercase">Analisis Peredaran</span>
                            <h2 class="text-title-md font-semibold text-ink">Tren Penjualan & Pembacaan Imprint</h2>
                        </div>
                        <div class="flex items-center gap-1 rounded-full bg-field p-1 text-caption">
                            <button
                                v-for="imp in ['all', 'sastra', 'lentera', 'akademia']"
                                :key="imp"
                                type="button"
                                class="rounded-full px-3 py-1 transition-colors"
                                :class="activeImprint === imp ? 'bg-ink text-white' : 'text-muted hover:text-ink'"
                                @click="activeImprint = imp"
                            >
                                {{ imp === 'all' ? 'Semua' : imp === 'sastra' ? 'Matapena Sastra' : imp === 'lentera' ? 'Lentera' : 'Akademia' }}
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between rounded-full bg-canvas p-space-sm px-space-md">
                        <div class="flex items-center gap-space-sm">
                            <span class="h-2 w-2 rounded-full bg-accent"></span>
                            <span class="text-caption font-semibold text-ink">Rekor Tertinggi:</span>
                            <span class="text-caption text-muted">12 Nov: Rp 18.400.000 / 380 eks</span>
                        </div>
                        <span class="rounded-full bg-field px-2.5 py-0.5 text-accent text-badge-label">Lonjakan +310%</span>
                    </div>
                    <div class="flex h-56 items-end justify-between gap-2 px-2 pt-6 md:gap-3">
                        <div v-for="(height, i) in salesBars" :key="i" class="group flex flex-1 flex-col items-center gap-2">
                            <div
                                class="relative w-full rounded-t-sm transition-all"
                                :class="i === peakIndex ? 'bg-accent' : 'bg-hair hover:bg-ink'"
                                :style="{ height: height + 'px' }"
                            >
                                <div class="pointer-events-none absolute -top-7 left-1/2 -translate-x-1/2 rounded bg-ink px-1.5 py-0.5 text-[11px] whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100">Rp {{ (height / 10).toFixed(1) }}M</div>
                            </div>
                            <span class="text-[11px]" :class="i === peakIndex ? 'font-semibold text-accent' : 'text-muted'">{{ ['01 Nov', '04 Nov', '07 Nov', '12 Nov', '15 Nov', '18 Nov', '21 Nov', '24 Nov', '27 Nov', '30 Nov'][i] }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-space-sm border-t border-hair/60 pt-space-sm sm:grid-cols-3">
                        <div><span class="text-caption text-muted block">Rata-rata Transaksi Harian</span><span class="text-action font-semibold text-ink">Rp 6.140.000 / hari</span></div>
                        <div><span class="text-caption text-muted block">Konversi Preview Sample</span><span class="text-action font-semibold text-ink">58.6% <span class="text-caption font-normal text-accent">(+4.1%)</span></span></div>
                        <div><span class="text-caption text-muted block">Rasio Baca Tuntas (>80%)</span><span class="text-action font-semibold text-ink">72.4% pembaca aktif</span></div>
                    </div>
                </div>

                <div class="flex flex-col justify-between gap-space-lg rounded-lg bg-soft p-space-lg lg:col-span-4">
                    <div class="flex flex-col gap-space-xs">
                        <span class="text-badge-label tracking-wider text-muted uppercase">Komposisi Pendapatan</span>
                        <h2 class="text-title-md font-semibold text-ink">Distribusi Imprint & Lisensi</h2>
                    </div>
                    <div class="flex flex-col gap-space-md">
                        <div v-for="imprint in imprints" :key="imprint.name" class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between text-body-sm">
                                <span class="text-action text-ink">{{ imprint.name }}</span>
                                <span class="text-action font-semibold text-ink">{{ imprint.pct }}% <span class="text-caption font-normal text-muted">({{ imprint.amount }})</span></span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-canvas">
                                <div class="h-full rounded-full" :class="imprint.color" :style="{ width: imprint.pct + '%' }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-space-sm rounded bg-canvas p-space-md">
                        <span class="text-caption tracking-wider text-muted uppercase">Model Pembelian Pembaca</span>
                        <div class="flex items-center justify-between text-body-sm">
                            <div class="flex items-center gap-space-xs"><span class="h-2.5 w-2.5 rounded-full bg-ink"></span><span class="text-ink">Hak Milik Permanen</span></div>
                            <span class="text-action font-semibold text-ink">82%</span>
                        </div>
                        <div class="flex items-center justify-between text-body-sm">
                            <div class="flex items-center gap-space-xs"><span class="h-2.5 w-2.5 rounded-full bg-accent"></span><span class="text-ink">Lisensi Kampus / Institusi</span></div>
                            <span class="text-action font-semibold text-ink">18%</span>
                        </div>
                        <p class="pt-1 text-caption text-muted">7 kemitraan universitas aktif berlangganan paket e-Library semester ini.</p>
                    </div>
                    <button type="button" class="flex h-10 w-full items-center justify-center gap-space-xs rounded-full bg-field text-body-sm text-action text-ink transition-colors hover:bg-canvas">
                        <span>Kelola Pembagian Imprint</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </button>
                </div>
            </section>

            <section class="flex flex-col gap-space-md">
                <div class="flex flex-col justify-between gap-space-md md:flex-row md:items-center">
                    <div>
                        <span class="text-badge-label tracking-wider text-muted uppercase">Katalog Utama</span>
                        <h2 class="text-title-md font-semibold text-ink">Kinerja Judul & Status Hak Cipta</h2>
                    </div>
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <button type="button" class="h-9 rounded-full bg-ink px-space-md text-caption text-white transition-all">Semua Judul (48)</button>
                        <button type="button" class="h-9 rounded-full bg-soft px-space-md text-caption text-ink transition-all hover:bg-field">Bestseller (8)</button>
                        <button type="button" class="h-9 rounded-full bg-soft px-space-md text-caption text-ink transition-all hover:bg-field">Imprint Sastra (24)</button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-lg bg-soft">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="text-caption tracking-wider text-muted uppercase">
                                <th class="py-space-md px-space-lg">Judul Buku & Pengarang</th>
                                <th class="py-space-md px-space-md">ISBN & Imprint</th>
                                <th class="py-space-md px-space-md text-right">Terjual</th>
                                <th class="py-space-md px-space-md text-right">Harga</th>
                                <th class="py-space-md px-space-md text-right">Total Bruto</th>
                                <th class="py-space-md px-space-md text-center">Status</th>
                                <th class="py-space-md px-space-lg text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft text-body-sm">
                            <tr v-for="book in catalogBooks" :key="book.isbn" class="transition-colors hover:bg-canvas">
                                <td class="py-space-md px-space-lg">
                                    <div class="flex items-center gap-space-md">
                                        <div class="h-16 w-11 flex-shrink-0 rounded bg-field shadow-sm"></div>
                                        <div class="flex min-w-0 flex-col">
                                            <span class="truncate font-semibold text-ink">{{ book.title }}</span>
                                            <span class="truncate text-caption text-muted">{{ book.author }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-space-md px-space-md">
                                    <span class="block font-mono text-caption text-ink">{{ book.isbn }}</span>
                                    <span class="text-caption text-muted">{{ book.imprint }}</span>
                                </td>
                                <td class="py-space-md px-space-md text-right text-ink">{{ book.sold }} eks</td>
                                <td class="py-space-md px-space-md text-right text-muted">{{ book.price }}</td>
                                <td class="py-space-md px-space-md text-right font-semibold text-ink">{{ book.gross }}</td>
                                <td class="py-space-md px-space-md text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-badge-label" :class="book.statusColor">
                                        {{ book.status }}
                                    </span>
                                </td>
                                <td class="py-space-md px-space-lg text-right">
                                    <div class="flex items-center justify-end gap-space-xs">
                                        <button type="button" class="h-8 rounded-full bg-field px-3 text-caption text-ink transition-colors hover:bg-soft">Edit</button>
                                        <button type="button" class="h-8 rounded-full bg-canvas px-3 text-caption text-ink transition-colors hover:bg-ink hover:text-white">Royalti</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex flex-col items-center justify-between gap-space-sm border-t border-hair/60 p-space-md px-space-lg sm:flex-row">
                        <span class="text-caption text-muted">Menampilkan 4 dari total 48 naskah terdaftar</span>
                        <button type="button" class="flex items-center gap-1 rounded-full bg-canvas px-space-md py-1.5 text-body-sm text-action text-ink transition-colors hover:bg-ink hover:text-white">
                            <span>Lihat Semua 48 Judul</span>
                            <span class="material-symbols-outlined text-[16px]">north_east</span>
                        </button>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-space-lg lg:grid-cols-2">
                <div class="flex flex-col justify-between gap-space-lg rounded-lg bg-soft p-space-lg">
                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-badge-label tracking-wider text-muted uppercase">Bagi Hasil & Kontrak</span>
                            <span class="flex items-center gap-1 rounded-full bg-accent px-2.5 py-0.5 text-badge-label text-white">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                Escrow Otomatis
                            </span>
                        </div>
                        <h2 class="text-title-md font-semibold text-ink">Manajemen Royalti Penulis Mitra</h2>
                        <p class="text-caption text-muted">Pencairan dilakukan berkala setiap tanggal 15 melalui virtual account terdaftar.</p>
                    </div>
                    <div class="flex flex-col gap-space-sm">
                        <div v-for="author in authorPayouts" :key="author.name" class="flex items-center justify-between rounded bg-canvas p-space-md">
                            <div class="flex items-center gap-space-md">
                                <div class="grid h-10 w-10 place-items-center rounded-full bg-field font-semibold text-ink">{{ author.initials }}</div>
                                <div class="flex flex-col">
                                    <span class="text-body-sm font-semibold text-ink">{{ author.name }}</span>
                                    <span class="text-caption text-muted">Kontrak: {{ author.contract }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-body-sm font-semibold text-ink">{{ author.amount }}</span>
                                <span class="text-badge-label" :class="author.statusColor">{{ author.status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-sm pt-space-xs">
                        <button type="button" class="h-10 flex-1 rounded-full bg-canvas text-body-sm text-action text-ink transition-colors hover:bg-field">Kelola Kontrak Penulis</button>
                        <button type="button" class="h-10 flex-1 rounded-full bg-field text-body-sm text-action text-ink transition-colors hover:bg-canvas">Rekonsiliasi Pajak</button>
                    </div>
                </div>

                <div class="flex flex-col justify-between gap-space-lg rounded-lg bg-soft p-space-lg">
                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-badge-label tracking-wider text-muted uppercase">Legalitas & Proteksi</span>
                            <span class="rounded-full bg-field px-2.5 py-0.5 text-badge-label text-ink">2 Dalam Antrean</span>
                        </div>
                        <h2 class="text-title-md font-semibold text-ink">Pengajuan ISBN, DRM & Persetujuan Naskah</h2>
                        <p class="text-caption text-muted">Enkripsi Readium LCP dan integrasi otomatis metadata Perpusnas RI.</p>
                    </div>
                    <div class="flex flex-col gap-space-sm">
                        <div v-for="sub in submissions" :key="sub.title" class="flex flex-col gap-space-sm rounded bg-canvas p-space-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="block text-body-sm font-semibold text-ink">{{ sub.title }}</span>
                                    <span class="text-caption text-muted">{{ sub.format }} • Penulis: {{ sub.author }}</span>
                                </div>
                                <span class="text-body-sm font-semibold" :class="sub.progress === 75 ? 'text-accent' : 'text-ink'">{{ sub.progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-soft">
                                <div class="h-full rounded-full" :class="sub.progress === 75 ? 'bg-accent' : 'bg-ink'" :style="{ width: sub.progress + '%' }"></div>
                            </div>
                            <div class="flex items-center gap-space-xs text-caption text-muted">
                                <span class="material-symbols-outlined text-[16px]" :class="sub.statusColor">{{ sub.statusIcon }}</span>
                                <span>{{ sub.status }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="flex h-10 w-full items-center justify-center gap-space-xs rounded-full bg-ink text-body-sm text-action text-white transition-opacity hover:opacity-90">
                        <span class="material-symbols-outlined text-[18px]">upload_file</span>
                        <span>Kirim Berkas Naskah Tambahan</span>
                    </button>
                </div>
            </section>

            <footer class="flex flex-col items-center justify-between gap-space-sm rounded bg-soft p-space-md px-space-lg text-caption text-muted md:flex-row">
                <div class="flex items-center gap-space-sm">
                    <span class="material-symbols-outlined text-[16px] text-accent">verified_user</span>
                    <span>Perjanjian Lisensi Penerbit Resmi ID-PUB-8810 • Terhubung Smart-Ledger Escrow PerpusKita</span>
                </div>
                <div class="flex items-center gap-space-md">
                    <span>Standar Perlindungan DRM: ISO/IEC 19757</span>
                    <span>•</span>
                    <a href="#" class=" text-ink transition-colors hover:underline">Ketentuan Bagi Hasil</a>
                </div>
            </footer>
        </div>
    </AuthenticatedLayout>
</template>
