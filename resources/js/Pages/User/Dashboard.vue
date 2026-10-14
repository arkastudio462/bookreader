<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import CoverImage from '@/Components/CoverImage.vue';

const props = defineProps({
    library: { type: Array, default: () => [] },
});

const activeTab = ref('reading');
const collectionFilter = ref('all');

const tabs = [
    { id: 'reading', label: 'Sedang Dibaca' },
    { id: 'collection', label: 'Koleksi Saya' },
    { id: 'notes', label: 'Catatan & Sorotan' },
    { id: 'history', label: 'Riwayat Transaksi' },
    { id: 'wishlist', label: 'Wishlist' },
];

const metrics = computed(() => [
    { label: 'Buku Koleksi', value: '24', unit: 'Buku', sub: '18 judul selesai dibaca penuh', icon: 'menu_book' },
    { label: 'Waktu Membaca', value: '142', unit: 'Jam', sub: 'Rata-rata 45 menit / hari', icon: 'schedule' },
    { label: 'Poin PerpusKita', value: '1.450', unit: '', sub: 'Setara diskon saldo Rp14.500', icon: 'local_activity', badge: 'Tersedia' },
    { label: 'Target Baca 2025', value: '16', unit: '/ 25 Buku', sub: '64% tercapai', icon: null, progress: 64 },
]);

const inProgressBooks = computed(() => props.library.filter(b => b.percent > 0 && b.percent < 100));
const currentBook = computed(() => inProgressBooks.value[0] || props.library[0] || null);
const otherInProgress = computed(() => inProgressBooks.value.slice(1, 3));

const collectionFilters = [
    { id: 'all', label: 'Semua' },
    { id: 'finished', label: 'Selesai Dibaca' },
    { id: 'unread', label: 'Belum Dibuka' },
    { id: 'epub', label: 'Format EPUB 3' },
    { id: 'pdf', label: 'Format PDF' },
];

const highlights = [
    {
        color: 'bg-accent',
        bookTitle: 'Hujan di Atas Kota Lama',
        date: '12 Nov 2024',
        quote: 'Di sudut jalan yang remang, sesosok bayangan berpayung sutra hitam menunggunya. Bukan untuk membalas dendam, melainkan menyerahkan sepucuk surat bersegel lilin merah yang telah disimpan tiga dekade.',
        meta: 'Bab 3 • Halaman 18 • Sorotan Warna Kuning',
    },
    {
        color: 'bg-ink',
        bookTitle: 'Filosofi Teras Kota',
        date: '28 Okt 2024',
        quote: 'Ketenangan bukan ketiadaan masalah, melainkan kemampuan menguasai respons batin saat kebisingan kota terus berusaha merampas kendali diri kita.',
        meta: 'Bab 6 • Halaman 84 • Ditandai Penting',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Dasbor Pembaca</h2>
        </template>

        <div class="mx-auto w-full max-w-[1440px] px-margin-lg py-space-xl">
            <section class="mb-space-xl w-full rounded-lg bg-soft p-space-md md:p-space-xl">
                <div class="flex flex-col justify-between gap-space-lg lg:flex-row lg:items-center">
                    <div class="flex flex-col items-start gap-space-lg sm:flex-row sm:items-center">
                        <div class="relative">
                            <div class="h-20 w-20 overflow-hidden rounded-full bg-field p-1 md:h-24 md:w-24">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAioRMDKT62EITTkEey-jyzFZphwSnzy92_Oi2KUp89CiewBFxNv_0TKOPQ2JuecflqgIFOmjIV489nl14a07lvevqFzJNov-i1UwPuH6SwRorjcoCwl0CuDn7cxERFLpAN3S2zbNpuICclv-tmPyMOJbZjHrjf9t6NZxn84V1HoEQzQzcTftNTPy60UADjJ-_BF7f25XtxPZK9NKdTa99uqIaaNuQxUml4gg-IGp8Q6wbsbaf4snInSg" alt="Avatar" class="h-full w-full rounded-full object-cover" />
                            </div>
                            <div class="absolute right-0 bottom-0 grid h-6 w-6 place-items-center rounded-full bg-accent text-white">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-space-xs">
                            <div class="flex flex-wrap items-center gap-space-xs">
                                <h1 class="text-title-lg tracking-tight text-ink">Dimas Nugraha</h1>
                                <span class="text-body-sm text-muted">@dimasnugraha</span>
                            </div>
                            <p class="max-w-xl text-body-sm text-ink-soft">Penikmat sastra kontemporer, kajian sejarah nusantara, dan esai filsafat urban.</p>
                            <div class="flex flex-wrap items-center gap-space-xs pt-space-xs">
                                <span class="flex items-center gap-1 rounded-full bg-ink px-space-sm py-1 text-badge-label text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Pembaca Aktif
                                </span>
                                <span class="flex items-center gap-1 rounded-full bg-field px-space-sm py-1 text-badge-label text-ink">
                                    <span class="material-symbols-outlined text-[14px] text-accent">check_circle</span>
                                    Akun Terverifikasi
                                </span>
                                <span class="rounded-full bg-canvas px-space-sm py-1 text-badge-label text-muted">Member sejak Mar 2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex w-full flex-wrap items-center gap-space-xs lg:w-auto">
                        <Link :href="route('profile.edit')" class="w-full rounded-full bg-canvas px-space-md py-space-sm text-center text-action text-ink transition-colors hover:bg-hairline-soft sm:w-auto">Edit Profil</Link>
                        <button type="button" class="flex w-full items-center justify-center gap-1 rounded-full bg-field px-space-md py-space-sm text-action text-ink transition-colors hover:bg-hair sm:w-auto">
                            <span class="material-symbols-outlined text-[18px]">settings</span>
                            Pengaturan
                        </button>
                        <Link :href="route('register')" class="flex w-full items-center justify-center gap-1 rounded-full px-space-md py-space-sm text-action text-muted transition-colors hover:text-ink sm:w-auto">
                            Beralih ke Penulis
                            <span class="material-symbols-outlined text-[16px]">north_east</span>
                        </Link>
                    </div>
                </div>

                <div class="mt-space-xl grid grid-cols-1 gap-space-md border-t border-hair pt-space-lg sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="metric in metrics" :key="metric.label" class="flex flex-col justify-between rounded-lg bg-canvas p-space-md">
                        <div class="mb-space-sm flex items-center justify-between text-muted">
                            <span class="text-body-sm">{{ metric.label }}</span>
                            <span v-if="metric.icon" class="material-symbols-outlined text-[20px]" :class="{ 'text-accent': metric.badge }">{{ metric.icon }}</span>
                            <span v-if="metric.progress" class="rounded-full bg-field px-2 py-0.5 text-badge-label text-ink">{{ metric.progress }}%</span>
                        </div>
                        <div>
                            <div class="flex items-baseline gap-space-xs">
                                <span class="text-title-lg font-semibold text-ink">{{ metric.value }}</span>
                                <span v-if="metric.unit" class="text-body-sm font-normal text-muted">{{ metric.unit }}</span>
                                <span v-if="metric.badge" class="rounded-full bg-accent px-2 py-0.5 text-badge-label text-white">{{ metric.badge }}</span>
                            </div>
                            <div v-if="metric.progress" class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-field">
                                <div class="h-full rounded-full bg-ink" :style="{ width: metric.progress + '%' }"></div>
                            </div>
                            <div class="mt-1 text-caption text-muted">{{ metric.sub }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mb-space-xl flex w-full items-center justify-start overflow-x-auto pb-space-sm">
                <div class="flex min-w-max items-center gap-1 rounded-full bg-field p-1">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        class="rounded-full px-space-md py-space-xs text-action transition-all"
                        :class="activeTab === tab.id ? 'bg-canvas text-ink' : 'text-muted hover:text-ink'"
                        @click="activeTab = tab.id"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <section v-if="activeTab === 'reading'" class="mb-space-xl">
                <div class="mb-space-md flex items-center justify-between">
                    <div>
                        <span class="text-caption tracking-wider text-muted uppercase block">Aktivitas Terkini</span>
                        <h2 class="text-title-lg tracking-tight text-ink">Lanjutkan Membaca</h2>
                    </div>
                    <div class="flex items-center gap-1 text-body-sm text-muted">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-accent"></span>
                        <span>Safari Reader • Tersinkronisasi</span>
                    </div>
                </div>

                <div v-if="currentBook" class="mb-space-lg rounded-lg bg-soft p-space-md md:p-space-lg">
                    <div class="grid grid-cols-1 items-center gap-space-lg md:grid-cols-12">
                        <div class="flex justify-center md:col-span-4 lg:col-span-3">
                            <Link :href="route('reader.show', currentBook.id)" class="relative aspect-[3/4] w-48 overflow-hidden rounded bg-field sm:w-56">
                                <img v-if="currentBook.cover_url" :src="currentBook.cover_url" :alt="currentBook.title" class="h-full w-full object-cover" />
                                <CoverImage v-else :book="currentBook" />
                                <div class="absolute top-2 left-2 rounded-full bg-scrim px-2 py-0.5 text-badge-label text-white backdrop-blur-sm">EPUB 3</div>
                            </Link>
                        </div>
                        <div class="flex h-full flex-col justify-between py-space-xs md:col-span-8 lg:col-span-9">
                            <div>
                                <div class="mb-space-xs flex flex-wrap items-center gap-space-xs">
                                    <span class="text-badge-label tracking-wide text-muted uppercase">Fiksi Sejarah & Drama</span>
                                    <span class="text-muted">•</span>
                                    <span class="text-caption text-muted">Terakhir dibuka tadi malam, 22:45 WIB</span>
                                </div>
                                <h3 class="mb-1 text-headline-sm tracking-tight text-ink">{{ currentBook.title }}</h3>
                                <p class="mb-space-md text-body-md text-muted">{{ currentBook.author_name }}</p>
                                <div class="mb-space-lg rounded bg-canvas p-space-md">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-action text-ink">Bab 14: Pertemuan di Kedai Teh Oei</span>
                                        <span class="text-action text-accent">{{ currentBook.percent }}% Selesai</span>
                                    </div>
                                    <div class="mb-2 h-2 w-full overflow-hidden rounded-full bg-field">
                                        <div class="h-full rounded-full bg-accent transition-all" :style="{ width: currentBook.percent + '%' }"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-caption text-muted">
                                        <span>Halaman 184 dari 272 Halaman</span>
                                        <span>Sisa estimasi membaca: ~48 menit</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-space-xs">
                                <Link :href="route('reader.show', currentBook.id)" class="flex items-center gap-2 rounded-full bg-ink px-space-lg py-space-sm text-action text-white transition-opacity hover:opacity-90">
                                    <span>Lanjutkan Membaca</span>
                                    <span class="material-symbols-outlined text-[18px]">north_east</span>
                                </Link>
                                <button type="button" class="flex items-center gap-1.5 rounded-full bg-canvas px-space-md py-space-sm text-action text-ink transition-colors hover:bg-field">
                                    <span class="material-symbols-outlined text-[18px] text-muted">download_for_offline</span>
                                    <span>Unduh (14.2 MB)</span>
                                </button>
                                <button type="button" class="flex items-center gap-1.5 rounded-full bg-canvas px-space-md py-space-sm text-action text-ink transition-colors hover:bg-field">
                                    <span class="material-symbols-outlined text-[18px] text-muted">sticky_note_2</span>
                                    <span>Catatan (6)</span>
                                </button>
                                <button type="button" title="Bookmark" class="grid h-11 w-11 place-items-center rounded-full bg-canvas text-ink transition-colors hover:bg-field">
                                    <span class="material-symbols-outlined text-[20px]">bookmark</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="otherInProgress.length" class="grid grid-cols-1 gap-space-md md:grid-cols-2">
                    <div v-for="book in otherInProgress" :key="book.id" class="flex items-center gap-space-md rounded-lg bg-canvas p-space-md transition-colors hover:bg-soft">
                        <Link :href="route('reader.show', book.id)" class="h-20 w-16 flex-shrink-0 overflow-hidden rounded bg-field">
                            <img v-if="book.cover_url" :src="book.cover_url" :alt="book.title" class="h-full w-full object-cover" />
                            <CoverImage v-else :book="book" />
                        </Link>
                        <div class="flex min-w-0 flex-grow flex-col justify-between">
                            <div>
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="truncate text-caption text-muted">{{ book.category?.name || 'Fiksi' }}</span>
                                    <span class="text-badge-label text-ink">{{ book.percent }}%</span>
                                </div>
                                <h4 class="truncate text-title-md text-ink">{{ book.title }}</h4>
                                <p class="truncate text-body-sm text-muted">{{ book.author_name }}</p>
                            </div>
                            <div class="mt-space-sm flex items-center justify-between gap-space-md">
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-field">
                                    <div class="h-full rounded-full bg-ink" :style="{ width: book.percent + '%' }"></div>
                                </div>
                                <Link :href="route('reader.show', book.id)" class="flex-shrink-0 rounded-full bg-field px-space-sm py-1 text-badge-label text-ink transition-colors hover:bg-hair">Buka</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="activeTab === 'collection'" class="mb-space-xl">
                <div class="mb-space-lg flex flex-col justify-between gap-space-md md:flex-row md:items-end">
                    <div>
                        <span class="text-caption tracking-wider text-muted uppercase block">Perpustakaan Pribadi</span>
                        <h2 class="text-title-lg tracking-tight text-ink">Koleksi Terbaru di Rakmu</h2>
                    </div>
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                        <button
                            v-for="filter in collectionFilters"
                            :key="filter.id"
                            type="button"
                            class="shrink-0 rounded-full px-space-md py-1 text-badge-label whitespace-nowrap transition-colors"
                            :class="collectionFilter === filter.id ? 'bg-ink text-white' : 'bg-field text-ink hover:bg-soft'"
                            @click="collectionFilter = filter.id"
                        >
                            {{ filter.label }}
                        </button>
                    </div>
                </div>

                <div v-if="library.length" class="grid grid-cols-2 gap-space-md sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <div v-for="book in library.slice(0, 10)" :key="book.id" class="group flex flex-col rounded-lg bg-canvas p-space-sm transition-colors hover:bg-soft">
                        <Link :href="route('reader.show', book.id)" class="relative mb-space-sm aspect-[3/4] overflow-hidden rounded bg-field">
                            <img v-if="book.cover_url" :src="book.cover_url" :alt="book.title" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                            <CoverImage v-else :book="book" />
                            <div v-if="book.percent === 100" class="absolute top-2 right-2 rounded-full bg-scrim px-2 py-0.5 text-badge-label text-white backdrop-blur-sm">Selesai</div>
                            <div v-else-if="book.percent > 0" class="absolute right-2 bottom-2 left-2 h-1 overflow-hidden rounded-full bg-field">
                                <div class="h-full rounded-full bg-accent" :style="{ width: book.percent + '%' }"></div>
                            </div>
                        </Link>
                        <h4 class="text-action text-ink truncate mb-0.5">{{ book.title }}</h4>
                        <span class="text-body-sm text-muted truncate mb-2">{{ book.author_name }}</span>
                        <div class="mt-auto flex items-center justify-between pt-2">
                            <span class="text-caption text-muted">{{ book.percent > 0 ? book.percent + '%' : 'Belum dibaca' }}</span>
                            <Link :href="route('reader.show', book.id)" class="grid h-7 w-7 place-items-center rounded-full bg-field text-ink transition-colors hover:bg-ink hover:text-white">
                                <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else class="py-16 text-center">
                    <p class="text-title-lg text-ink">Belum ada buku di koleksi.</p>
                    <Link :href="route('catalog.index')" class="btn-primary mt-4 inline-flex">Jelajahi Katalog</Link>
                </div>
            </section>

            <section v-if="activeTab === 'notes'" class="mb-space-xl">
                <div class="mb-space-lg flex flex-col justify-between gap-space-sm sm:flex-row sm:items-center">
                    <div>
                        <span class="text-caption tracking-wider text-muted uppercase block">Sorotan Bacaan</span>
                        <h2 class="text-title-lg tracking-tight text-ink">Kutipan & Catatan Emas Terakhir</h2>
                    </div>
                    <button type="button" class="flex items-center gap-2 self-start rounded-full bg-field px-space-md py-space-sm text-action transition-colors hover:bg-hair sm:self-auto">
                        <span class="material-symbols-outlined text-[18px]">ios_share</span>
                        <span>Ekspor Semua Catatan</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-space-md md:grid-cols-2">
                    <div v-for="highlight in highlights" :key="highlight.bookTitle" class="flex flex-col justify-between rounded-lg bg-soft p-space-lg">
                        <div>
                            <div class="mb-space-sm flex items-center justify-between">
                                <div class="flex items-center gap-space-xs">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="highlight.color"></span>
                                    <span class="text-badge-label text-ink">{{ highlight.bookTitle }}</span>
                                </div>
                                <span class="text-caption text-muted">{{ highlight.date }}</span>
                            </div>
                            <blockquote class="text-body-lead text-ink italic mb-space-md">"{{ highlight.quote }}"</blockquote>
                        </div>
                        <div class="flex items-center justify-between pt-space-sm text-muted text-caption">
                            <span>{{ highlight.meta }}</span>
                            <div class="flex items-center gap-space-xs">
                                <button type="button" title="Salin" class="p-1 transition-colors hover:text-ink"><span class="material-symbols-outlined text-[18px]">content_copy</span></button>
                                <button type="button" title="Buka di Reader" class="p-1 transition-colors hover:text-ink"><span class="material-symbols-outlined text-[18px]">open_in_new</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-space-xl rounded-lg bg-field p-space-lg">
                <div class="flex flex-col justify-between gap-space-lg lg:flex-row lg:items-center">
                    <div class="flex flex-col items-start gap-space-lg sm:flex-row sm:items-center">
                        <div class="grid h-12 w-12 flex-shrink-0 place-items-center rounded-full bg-canvas text-ink">
                            <span class="material-symbols-outlined text-[24px]">cloud_sync</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="text-title-md text-ink">Perangkat Terhubung & Sinkronisasi Otomatis</h4>
                            <div class="flex flex-wrap items-center gap-space-sm text-body-sm text-muted">
                                <div class="flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full bg-accent"></span>
                                    <span class="text-ink">MacBook Pro 14"</span>
                                    <span>— Aktif sekarang</span>
                                </div>
                                <span>•</span>
                                <div class="flex items-center gap-1">
                                    <span>iPhone 15 Pro</span>
                                    <span>— Sinkron 2 jam lalu</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="min-w-[280px] rounded bg-canvas p-space-md">
                        <div class="mb-1.5 flex items-center justify-between text-caption">
                            <span class="text-muted">Penyimpanan Offline</span>
                            <span class="font-semibold text-ink">148 MB / 2.0 GB</span>
                        </div>
                        <div class="mb-2 h-1.5 w-full overflow-hidden rounded-full bg-field">
                            <div class="h-full rounded-full bg-accent" style="width: 7.4%"></div>
                        </div>
                        <div class="flex items-center justify-between text-caption text-muted">
                            <span>5 berkas tersimpan offline</span>
                            <a href="#" class="font-semibold transition-colors hover:text-ink">Kelola Berkas</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
