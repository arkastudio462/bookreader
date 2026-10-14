<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    stats: { type: Object, default: () => ({}) },
});

const activePeriod = ref('30');
const activeChartMetric = ref('revenue');

const kpis = [
    { label: 'Perputaran Bruto (GMV)', value: 'Rp 428.650.000', badge: '+18.4% bln lalu', sub: 'Bagi hasil bersih platform (30%): Rp 128.595.000', borderColor: 'hover:border-ink' },
    { label: 'Total Pembaca Aktif', value: '89.420', badge: '+4.120 mendaftar', sub: 'Rasio membaca >30 mnt/hari: 64.8%', borderColor: 'hover:border-ink' },
    { label: 'Antrean Moderasi Naskah', value: '18 Naskah Baru', badge: '8 Butuh Verifikasi', sub: 'SLA respon moderasi kurator: 3.4 jam kerja', borderColor: 'hover:border-accent' },
    { label: 'Likuiditas Payout Mitra', value: 'Rp 86.420.000', badge: '24 Pengajuan', sub: 'Pencairan otomatis: Selasa & Jumat', borderColor: 'hover:border-ink' },
];

const revenueAllocation = [
    { label: '70% Royalti Penulis & Penerbit', amount: 'Rp 300.055.000', pct: 70, color: 'bg-ink', sub: '1.240 judul terdaftar menerima akrual dividen' },
    { label: '20% CDN DRM & Cloud Reader Edge', amount: 'Rp 85.730.000', pct: 20, color: 'bg-muted', sub: 'Enkripsi AES-256 berkas digital & transmisi bandwidth' },
    { label: '10% Margin Bersih Platform', amount: 'Rp 42.865.000', pct: 10, color: 'bg-accent', sub: 'Operasional audit kurasi, pengembangan sistem' },
];

const moderationQueue = [
    { title: 'Bunga Api di Lembah Merapi', category: 'Fiksi Sejarah • ISBN 978-623-01-2900-1', author: 'Dian Sastrowardoyo', publisher: 'Matapena Press (Mitra Gold)', format: 'EPUB 3 + 3 Bab Sampel', time: '2 jam lalu', status: 'Menunggu Review ISBN', statusColor: 'bg-amber-50 text-amber-900 border-amber-200', statusDot: 'bg-amber-600' },
    { title: 'Kriptografi & Kedaulatan Data', category: 'Teknologi & Komputer • 348 Halaman', author: 'Ir. Budi Rahardjo', publisher: 'Penerbit Mandiri (Terverifikasi)', format: 'PDF Interaktif (42 MB)', time: '4 jam lalu', status: 'Verifikasi Berkas DRM', statusColor: 'bg-blue-50 text-blue-900 border-blue-200', statusDot: 'bg-accent' },
    { title: 'Seni Negosiasi Pasar Tradisional', category: 'Bisnis & UMKM • ISBN 978-602-09-1234-8', author: 'Hendra Kusuma', publisher: 'Penerbit Narasi Nusantara', format: 'EPUB 3 Reflowable', time: '6 jam lalu', status: 'Verifikasi Royalti 70:30', statusColor: 'bg-emerald-50 text-emerald-900 border-emerald-200', statusDot: 'bg-emerald-600' },
    { title: 'Hikayat Hutan Keramat', category: 'Sastra Nusantara • Sampul Butuh Peningkatan DPI', author: 'Ratna Dewi Astuti', publisher: 'Penulis Mandiri Komunitas', format: 'EPUB 3 + Audio Sample', time: '1 hari lalu', status: 'Perlu Revisi Cover', statusColor: 'bg-rose-50 text-rose-900 border-rose-200', statusDot: 'bg-error' },
];

const liveLedger = [
    { icon: 'shopping_cart', iconColor: 'text-ink', buyer: 'Dimas N.', action: 'membeli lisensi digital', book: 'Hujan di Atas Kota Lama', split: 'Penulis Rp 34.300 • Platform Rp 14.700 • QRIS BCA', amount: '+Rp 49.000', time: '2 mnt lalu' },
    { icon: 'shopping_cart', iconColor: 'text-ink', buyer: 'Siti L.', action: 'membeli lisensi', book: 'Peta Nusantara Lama', split: 'Penulis Rp 43.400 • Platform Rp 18.600 • GoPay', amount: '+Rp 62.000', time: '5 mnt lalu' },
    { icon: 'auto_stories', iconColor: 'text-accent', buyer: 'Rian H.', action: 'sewa membaca 30 hari', book: 'Merawat Akal Sehat', split: 'Potong Saldo Pembaca • Lisensi DRM Aktif', amount: '+Rp 18.000', time: '11 mnt lalu' },
    { icon: 'shopping_cart', iconColor: 'text-ink', buyer: 'Taufik A.', action: 'membeli berkas', book: 'Arsitektur Batavia', split: 'Penerbit Rp 47.600 • Platform Rp 20.400 • Kartu Debit', amount: '+Rp 68.000', time: '18 mnt lalu' },
];

const payoutQueue = [
    { name: 'Arya Wirawan', bank: 'BCA •••• 9812', npwp: 'NPWP Terverifikasi', amount: 'Rp 12.820.000', status: 'Siap Proses', statusColor: 'bg-emerald-50 text-emerald-900' },
    { name: 'Matapena Press (PT)', bank: 'Mandiri •••• 4401', npwp: 'Faktur Pajak Tahap Klarifikasi', amount: 'Rp 34.500.000', status: 'Verifikasi Pajak', statusColor: 'bg-amber-50 text-amber-900' },
    { name: 'Rocky Gerung', bank: 'BNI •••• 2290', npwp: 'NPWP Terverifikasi', amount: 'Rp 8.750.000', status: 'Siap Proses', statusColor: 'bg-emerald-50 text-emerald-900' },
];

const systemNodes = [
    { icon: 'menu_book', name: 'Web Reader DRM Engine', status: 'Online 99.99% • Node Jakarta-1', dot: 'bg-emerald-500' },
    { icon: 'cloud_sync', name: 'Cloudflare CDN Edge Cache', status: 'Latensi 18ms • Cache Hit 94.2%', dot: 'bg-emerald-500' },
    { icon: 'database', name: 'Replikasi Database PostgreSQL', status: 'Sinkronisasi Normal • Lag 0.04s', dot: 'bg-emerald-500' },
];
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Dasbor Super Admin</h2>
        </template>

        <div class="space-y-space-lg p-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted" aria-label="Breadcrumb">
                        <span>Dasbor Operasional</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Ringkasan & Analitik Global</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Ringkasan Operasional & Ekosistem PerpusKita.</h1>
                    <p class="text-body-md text-muted">Pantau kesehatan platform, sirkulasi peredaran naskah, arus kas royalti mitra, dan moderasi.</p>
                </div>
                <div class="flex flex-wrap items-center gap-space-sm">
                    <div class="inline-flex items-center rounded-full border border-hairline-soft bg-field p-1">
                        <button
                            v-for="p in ['today', '7', '30', 'year']"
                            :key="p"
                            type="button"
                            class="rounded-full px-space-md py-1.5 text-body-sm text-action transition-colors"
                            :class="activePeriod === p ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'"
                            @click="activePeriod = p"
                        >
                            {{ p === 'today' ? 'Hari Ini' : p === '7' ? '7 Hari' : p === '30' ? '30 Hari' : 'Tahun 2025' }}
                        </button>
                    </div>
                    <button type="button" class="inline-flex items-center gap-space-xs rounded-full border border-hair bg-canvas px-space-md py-2.5 text-body-sm text-action text-ink transition-colors hover:bg-soft">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Ekspor Audit</span>
                    </button>
                    <button type="button" class="inline-flex items-center gap-space-xs rounded-full bg-ink px-space-lg py-2.5 text-body-sm text-action text-white transition-opacity hover:opacity-90">
                        <span class="material-symbols-outlined text-[18px]">campaign</span>
                        <span>+ Pengumuman</span>
                    </button>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-space-md md:grid-cols-2 xl:grid-cols-4">
                <article v-for="kpi in kpis" :key="kpi.label" class="flex flex-col justify-between rounded-[24px] border border-hair bg-canvas p-space-lg transition-colors" :class="kpi.borderColor">
                    <div class="space-y-space-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-muted text-caption tracking-wider uppercase">{{ kpi.label }}</span>
                            <span class="inline-flex items-center gap-0.5 rounded-full border border-hairline-soft bg-soft px-2.5 py-0.5 text-ink text-badge-label">
                                <span v-if="kpi.badge.includes('Verifikasi')" class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>
                                <span v-else-if="kpi.badge.includes('+')" class="material-symbols-outlined text-[14px]">trending_up</span>
                                {{ kpi.badge }}
                            </span>
                        </div>
                        <div class="pt-1 text-headline-sm font-semibold tracking-tight text-ink">{{ kpi.value }}</div>
                    </div>
                    <div class="mt-space-md flex items-center justify-between border-t border-hairline-soft pt-space-md text-muted text-body-sm">
                        <span>{{ kpi.sub.split(':')[0] }}</span>
                        <span class="font-semibold text-ink">{{ kpi.sub.split(':')[1] }}</span>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <article class="flex flex-col justify-between space-y-space-lg rounded-[24px] border border-hair bg-canvas p-space-lg lg:col-span-7">
                    <div class="flex flex-col justify-between gap-space-sm sm:flex-row sm:items-center">
                        <div>
                            <h2 class="text-title-md text-ink">Volume Transaksi & Pembacaan Digital</h2>
                            <p class="text-muted text-caption">Aktivitas lisensi komersial dan peminjaman aktif 12 pekan terakhir</p>
                        </div>
                        <div class="flex items-center gap-1 rounded-full bg-field p-1">
                            <button
                                v-for="m in ['revenue', 'license', 'activity']"
                                :key="m"
                                type="button"
                                class="rounded-full px-3 py-1 text-caption transition-colors"
                                :class="activeChartMetric === m ? 'bg-canvas text-ink shadow-sm' : 'text-muted hover:text-ink'"
                                @click="activeChartMetric = m"
                            >
                                {{ m === 'revenue' ? 'Pendapatan' : m === 'license' ? 'Lisensi' : 'Aktivitas' }}
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-hairline-soft bg-soft p-space-md">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-ink text-[20px]">equalizer</span>
                            <span class="text-body-sm font-medium text-ink">Rekor Penjualan Kampanye Hari Buku Nasional</span>
                        </div>
                        <div class="text-caption font-semibold text-ink">12 Nov: Rp 24.800.000 <span class="font-normal text-muted">(512 transaksi)</span></div>
                    </div>
                    <div class="flex h-56 items-end gap-2 px-2">
                        <div v-for="(height, i) in [80, 105, 90, 115, 146, 122, 100, 130, 110, 85, 120, 105, 110, 138]" :key="i" class="flex-1 rounded-t transition-colors" :class="i === 4 ? 'bg-accent' : i % 3 === 1 ? 'bg-ink' : 'bg-hair hover:bg-ink'" :style="{ height: height + 'px' }"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-space-sm border-t border-hairline-soft pt-space-md sm:grid-cols-3">
                        <div class="rounded-xl bg-field p-2.5"><div class="text-muted text-caption">Rata-rata Transaksi</div><div class="text-action text-body-md font-semibold text-ink">Rp 14.280.000 / hari</div></div>
                        <div class="rounded-xl bg-field p-2.5"><div class="text-muted text-caption">QRIS & GoPay</div><div class="text-action text-body-md font-semibold text-ink">74% Volume</div></div>
                        <div class="rounded-xl bg-field p-2.5"><div class="text-muted text-caption">Kartu & VA</div><div class="text-action text-body-md font-semibold text-ink">26% Volume</div></div>
                    </div>
                </article>

                <article class="flex flex-col justify-between space-y-space-lg rounded-[24px] border border-hair bg-soft p-space-lg lg:col-span-5">
                    <div class="space-y-space-xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-title-md text-ink">Struktur Bagi Hasil & Arus Kas</h2>
                            <span class="inline-flex items-center gap-1 rounded-full border border-hairline-soft bg-canvas px-2.5 py-1 text-ink text-caption font-semibold">
                                <span class="material-symbols-outlined text-[14px]">lock_reset</span>
                                Smart Ledger v2
                            </span>
                        </div>
                        <p class="text-muted text-caption">Penyaluran otomatis bruto ke akun royalti escrow penerbit</p>
                    </div>
                    <div class="space-y-space-md">
                        <div v-for="item in revenueAllocation" :key="item.label" class="space-y-2 rounded-2xl border border-hairline-soft bg-canvas p-space-md">
                            <div class="flex items-center justify-between">
                                <span class="text-body-sm text-action flex items-center gap-2 text-ink">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="item.color"></span>
                                    {{ item.label }}
                                </span>
                                <span class="text-body-sm font-semibold text-ink">{{ item.amount }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-field">
                                <div class="h-2 rounded-full" :class="item.color" :style="{ width: item.pct + '%' }"></div>
                            </div>
                            <div class="text-muted text-caption">{{ item.sub }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-sm rounded-xl bg-field p-space-sm text-muted text-caption">
                        <span class="material-symbols-outlined text-ink text-[18px]">verified</span>
                        <span>Semua buku terhubung ke smart-ledger otomatis. Bebas sengketa royalti dalam 90 hari terakhir.</span>
                    </div>
                </article>
            </section>

            <section class="space-y-space-lg rounded-[24px] border border-hair bg-canvas p-space-lg">
                <div class="flex flex-col justify-between gap-space-sm md:flex-row md:items-center">
                    <div class="space-y-1">
                        <div class="flex items-center gap-space-sm">
                            <h2 class="text-title-md text-ink">Moderasi Naskah Menunggu Persetujuan</h2>
                            <span class="rounded-full bg-accent px-2.5 py-0.5 text-white text-badge-label">8 Menunggu</span>
                        </div>
                        <p class="text-muted text-body-sm">Tinjau kelayakan isi naskah, lisensi hak cipta, dan kesesuaian berkas sebelum tayang.</p>
                    </div>
                    <div class="inline-flex rounded-full border border-hairline-soft bg-soft p-1 text-caption">
                        <button type="button" class="rounded-full bg-canvas px-3 py-1 text-ink shadow-sm">Semua Genre</button>
                        <button type="button" class="rounded-full px-3 py-1 text-muted transition-colors hover:text-ink">Penerbit Mandiri</button>
                        <button type="button" class="rounded-full px-3 py-1 text-muted transition-colors hover:text-ink">Mitra Resmi</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-hair text-muted text-caption tracking-wider uppercase">
                                <th class="px-space-md py-space-sm font-semibold">Judul Naskah</th>
                                <th class="px-space-md py-space-sm font-semibold">Penulis / Mitra</th>
                                <th class="px-space-md py-space-sm font-semibold">Format</th>
                                <th class="px-space-md py-space-sm font-semibold">Waktu</th>
                                <th class="px-space-md py-space-sm font-semibold">Status</th>
                                <th class="px-space-md py-space-sm text-right font-semibold">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft text-body-sm text-ink">
                            <tr v-for="item in moderationQueue" :key="item.title" class="transition-colors hover:bg-soft/60">
                                <td class="px-space-md py-space-md">
                                    <div class="flex items-center gap-space-md">
                                        <div class="h-16 w-12 flex-shrink-0 rounded-lg border border-hairline-soft bg-field shadow-sm"></div>
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-ink">{{ item.title }}</div>
                                            <div class="text-muted text-caption">{{ item.category }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-space-md py-space-md">
                                    <div class="font-medium text-ink">{{ item.author }}</div>
                                    <div class="text-muted text-caption">{{ item.publisher }}</div>
                                </td>
                                <td class="px-space-md py-space-md">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-field px-2.5 py-0.5 text-ink text-caption">{{ item.format }}</span>
                                </td>
                                <td class="px-space-md py-space-md text-muted">{{ item.time }}</td>
                                <td class="px-space-md py-space-md">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-badge-label" :class="item.statusColor">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="item.statusDot"></span>
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-md text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button type="button" class="rounded-full bg-ink px-3 py-1.5 text-white text-caption transition-opacity hover:opacity-90">Tinjau</button>
                                        <button type="button" class="rounded-full border border-hair bg-canvas px-3 py-1.5 text-ink text-caption transition-colors hover:bg-soft">Setujui</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col items-center justify-between gap-space-sm pt-space-sm text-muted text-caption sm:flex-row">
                    <span>Menampilkan 4 dari 8 naskah prioritas tinggi</span>
                    <div class="flex items-center gap-space-xs">
                        <button type="button" class="rounded-full bg-field px-3 py-1.5 text-ink transition-colors hover:bg-soft">Sebelumnya</button>
                        <span class="px-2 font-medium text-ink">1 / 2</span>
                        <button type="button" class="rounded-full bg-field px-3 py-1.5 text-ink transition-colors hover:bg-soft">Berikutnya</button>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-space-lg lg:grid-cols-2">
                <article class="flex flex-col justify-between space-y-space-md rounded-[24px] border border-hair bg-canvas p-space-lg">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-space-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                                <h2 class="text-title-md text-ink">Ledger Transaksi Real-time</h2>
                            </div>
                            <p class="text-muted text-caption">Arus kas masuk & pemecahan bagi hasil instan</p>
                        </div>
                        <span class="text-muted text-caption">Sinkronisasi < 2s</span>
                    </div>
                    <div class="flex-1 space-y-space-sm">
                        <div v-for="event in liveLedger" :key="event.time" class="flex items-start justify-between gap-space-sm rounded-xl bg-field p-space-sm">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined mt-0.5 text-[20px]" :class="event.iconColor">{{ event.icon }}</span>
                                <div>
                                    <div class="text-body-sm font-semibold text-ink">{{ event.buyer }} <span class="font-normal text-muted">{{ event.action }}</span> "{{ event.book }}"</div>
                                    <div class="text-muted text-caption">{{ event.split }}</div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <div class="text-body-sm font-semibold text-ink">{{ event.amount }}</div>
                                <div class="text-muted text-caption">{{ event.time }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-space-xs">
                        <a href="#" class="inline-flex items-center gap-1 text-caption font-semibold text-ink transition-colors hover:underline">Buka Buku Besar Keuangan <span class="material-symbols-outlined text-[16px]">arrow_outward</span></a>
                    </div>
                </article>

                <article class="flex flex-col justify-between space-y-space-md rounded-[24px] border border-hair bg-canvas p-space-lg">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-space-xs">
                        <div class="space-y-0.5">
                            <h2 class="text-title-md text-ink">Pengajuan Payout Mitra</h2>
                            <p class="text-muted text-caption">Rekonsiliasi rekening bank mitra & validasi NPWP</p>
                        </div>
                        <span class="rounded-full bg-field px-2.5 py-0.5 text-ink text-caption font-semibold">Batch #094-FEB</span>
                    </div>
                    <div class="flex-1 space-y-space-sm">
                        <div v-for="payout in payoutQueue" :key="payout.name" class="flex items-center justify-between rounded-2xl border border-hairline-soft bg-canvas p-space-md">
                            <div class="space-y-0.5">
                                <div class="text-body-sm font-semibold text-ink">{{ payout.name }}</div>
                                <div class="text-muted text-caption">{{ payout.bank }} • {{ payout.npwp }}</div>
                            </div>
                            <div class="space-y-1 text-right">
                                <div class="text-body-sm font-bold text-ink">{{ payout.amount }}</div>
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-badge-label" :class="payout.statusColor">{{ payout.status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-between gap-space-sm pt-space-xs sm:flex-row">
                        <div class="text-muted text-caption">Penyaluran dana via API Bank Indonesia BI-FAST</div>
                        <button type="button" class="w-full rounded-full bg-ink px-space-md py-2.5 text-caption text-white transition-opacity hover:opacity-90 sm:w-auto">Proses Payout Terverifikasi</button>
                    </div>
                </article>
            </section>

            <section class="rounded-[20px] border border-hair bg-soft p-space-md">
                <div class="grid grid-cols-1 gap-space-md md:grid-cols-3">
                    <div v-for="node in systemNodes" :key="node.name" class="flex items-center gap-space-md rounded-xl border border-hairline-soft bg-canvas p-space-sm">
                        <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-full bg-field">
                            <span class="material-symbols-outlined text-ink text-[20px]">{{ node.icon }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="truncate text-body-sm font-semibold text-ink">{{ node.name }}</span>
                                <span class="h-2 w-2 rounded-full" :class="node.dot"></span>
                            </div>
                            <div class="text-muted text-caption">{{ node.status }}</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
