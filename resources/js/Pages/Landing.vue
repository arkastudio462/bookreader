<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import BookCard from '@/Components/BookCard.vue';
import CoverImage from '@/Components/CoverImage.vue';
import FlashSaleCountdown from '@/Components/FlashSaleCountdown.vue';

const props = defineProps({
    featuredBooks: Array,
    newestBooks: Array,
    flashSale: Object,
    popularCategories: Array,
    stats: Object,
});

const catalogTab = ref('top');
const books = computed(() => (catalogTab.value === 'top' ? props.featuredBooks : props.newestBooks));
const marqueeBooks = computed(() => [...props.featuredBooks, ...props.newestBooks]);
const heroBook = computed(() => props.featuredBooks[0] ?? null);
const heroCover = computed(() => {
    const covers = marqueeBooks.value.map((b) => b.cover_url).filter(Boolean);
    return covers.length > 0 ? covers[0] : null;
});

const nlEmail = ref('');
const nlMsg = ref('Kabar promo dan buku baru, sebulan sekali.');

function subscribe() {
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nlEmail.value.trim());
    nlMsg.value = ok
        ? 'Terima kasih, kamu sudah terdaftar.'
        : 'Masukkan alamat email yang valid, misalnya nama@email.com.';
    if (ok) nlEmail.value = '';
}

const features = [
    {
        title: 'Reader yang nyaman',
        desc: 'Baca EPUB dan PDF tanpa aplikasi. Progres tersimpan di semua perangkat, lengkap dengan bookmark, highlight, dan tema terang, sepia, atau gelap.',
    },
    {
        title: 'Voucher dan poin',
        desc: 'Pakai voucher dan tukar poin reward saat checkout. Poin didapat dari pembelian, streak membaca, dan ulasan.',
    },
    {
        title: 'Royalti transparan',
        desc: 'Setiap penjualan tercatat per transaksi. Penulis dan penerbit melihat saldo, riwayat, dan bisa menarik dana kapan saja.',
    },
];

const testimonials = [
    {
        name: 'Rina A.',
        role: 'Pembaca',
        quote: 'Lanjut baca di HP persis di halaman terakhir laptop. Sepele, tapi bikin saya pindah dari aplikasi lain.',
    },
    {
        name: 'Dimas Aryanto',
        role: 'Penulis',
        quote: 'Daftar sore, bukunya saya unggah malam itu juga. Laporan royalti per transaksinya jelas.',
    },
    {
        name: 'Penerbit Lentera',
        role: 'Penerbit',
        quote: 'Kemitraan dengan penulis baru jadi mudah. Persentase bagi hasil terlihat jelas untuk kedua pihak.',
    },
    {
        name: 'Sari P.',
        role: 'Pembaca',
        quote: 'Poin dari ulasan bisa dipakai potong harga. Flash sale jam tujuh malam jadi kebiasaan.',
    },
];

const statsList = computed(() => [
    { value: Number(props.stats.total_books).toLocaleString('id-ID'), label: 'Buku tersedia' },
    { value: Number(props.stats.total_authors).toLocaleString('id-ID'), label: 'Penulis dan penerbit' },
    { value: Number(props.stats.total_readers).toLocaleString('id-ID'), label: 'Pembaca aktif' },
]);
</script>

<template>
    <Head title="PerpusKita — Baca, beli, dan terbitkan buku digital" />

    <AppHeader />

    <main>
        <!-- HERO -->
        <section class="relative overflow-hidden px-5 pb-20 pt-16 md:pt-24">
            <div class="pointer-events-none absolute top-0 right-0 h-[500px] w-[500px] rounded-full bg-soft blur-3xl opacity-60" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-6xl">
                <div class="grid items-center gap-12 lg:grid-cols-[1fr_400px]">
                    <div>
                        <span class="badge-accent mb-6 inline-flex">
                            <span class="mr-2 inline-block h-2 w-2 rounded-full bg-white"></span>
                            Platform buku digital Indonesia
                        </span>
                        <h1 class="h max-w-4xl text-5xl sm:text-6xl md:text-[76px]">
                            Baca, beli, dan terbitkan buku.
                            <span class="lt block text-accent">Semuanya di satu tempat.</span>
                        </h1>
                        <p class="lt mt-6 max-w-xl text-xl leading-snug text-muted">
                            Toko buku digital dengan reader EPUB dan PDF langsung di browser, promo yang rutin
                            datang, dan bagi hasil yang jelas untuk penulis dan penerbit.
                        </p>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            <Link :href="route('catalog.index')" class="btn-primary">Mulai membaca</Link>
                            <Link :href="route('register')" class="btn-outline">Daftar penulis</Link>
                            <a href="#penulis" class="btn-soft">Jadi penerbit ↗</a>
                        </div>

                        <div class="mt-12 flex items-center gap-8">
                            <div>
                                <p class="h text-3xl">{{ Number(stats.total_books).toLocaleString('id-ID') }}+</p>
                                <p class="mt-1 text-sm text-muted">Buku tersedia</p>
                            </div>
                            <div class="h-10 w-px bg-hair"></div>
                            <div>
                                <p class="h text-3xl">{{ Number(stats.total_readers).toLocaleString('id-ID') }}+</p>
                                <p class="mt-1 text-sm text-muted">Pembaca aktif</p>
                            </div>
                            <div class="h-10 w-px bg-hair"></div>
                            <div>
                                <p class="h text-3xl">{{ Number(stats.total_authors).toLocaleString('id-ID') }}+</p>
                                <p class="mt-1 text-sm text-muted">Penulis & penerbit</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="heroBook" class="relative mx-auto hidden w-full max-w-[360px] lg:block">
                        <div class="absolute -inset-6 rounded-md bg-soft" aria-hidden="true"></div>
                        <div class="relative overflow-hidden rounded-sm shadow-2xl">
                            <img
                                v-if="heroBook.cover_url"
                                :src="heroBook.cover_url"
                                :alt="heroBook.title"
                                class="aspect-[3/4] w-full object-cover"
                            />
                            <CoverImage v-else :book="heroBook" />
                        </div>
                        <div class="card absolute -bottom-8 -left-12 flex items-center gap-3 py-3 pr-5 pl-3 shadow-xl">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-ink text-white">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                                </svg>
                            </span>
                            <div>
                                <p class="lk text-sm text-ink">{{ heroBook.title }}</p>
                                <p class="text-xs text-muted">{{ heroBook.author_name }}</p>
                            </div>
                        </div>
                        <div class="card absolute -top-4 -right-4 flex items-center gap-2 py-2 pr-4 pl-2 shadow-lg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-accent">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            <span class="lk text-sm text-ink">{{ heroBook.average_rating }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE COVERS -->
        <section class="overflow-hidden border-y border-hairline-soft py-6" aria-label="Buku pilihan">
            <div class="marquee flex w-max gap-4 pr-4">
                <Link
                    v-for="(book, i) in [...marqueeBooks, ...marqueeBooks]"
                    :key="i"
                    :href="route('catalog.show', book.slug)"
                    class="w-36 shrink-0 md:w-44"
                >
                    <img
                        v-if="book.cover_url"
                        :src="book.cover_url"
                        :alt="book.title"
                        class="aspect-[3/4] w-full rounded-sm object-cover"
                        loading="lazy"
                    />
                    <CoverImage v-else :book="book" />
                </Link>
            </div>
        </section>

        <!-- FLASH SALE -->
        <section
            v-if="flashSale && flashSale.items && flashSale.items.length"
            class="mx-auto max-w-6xl px-5 pt-20"
        >
            <div class="rounded-md bg-ink p-8 text-white md:p-12">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <span class="badge-accent mb-4 !bg-accent">
                            <span class="mr-2 inline-block h-2 w-2 animate-pulse rounded-full bg-white"></span>
                            Flash sale
                        </span>
                        <h2 class="h mt-4 text-4xl md:text-[44px]">
                            {{ flashSale.name || 'Harga turun, waktunya terbatas.' }}
                        </h2>
                        <p v-if="flashSale.banner_text" class="lt mt-3 text-xl text-faint">
                            {{ flashSale.banner_text }}
                        </p>
                    </div>
                    <div class="rounded-md bg-white/10 p-4">
                        <p class="lk mb-2 text-xs tracking-widest text-faint uppercase">Berakhir dalam</p>
                        <div class="flex items-center gap-2 text-white [&_div]:!bg-white/10 [&_div]:!text-white [&_span]:!text-faint">
                            <FlashSaleCountdown :ends-at="flashSale.ends_at" />
                        </div>
                    </div>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <BookCard
                        v-for="item in flashSale.items.slice(0, 5)"
                        :key="item.id"
                        :book="item.book"
                        :show-sold="true"
                    />
                </div>
            </div>
        </section>

        <!-- KOLEKSI & KATEGORI -->
        <section class="mx-auto max-w-6xl px-5 pt-28">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="h max-w-xl text-4xl md:text-[44px]">Jelajahi menurut kategori.</h2>
                    <p class="lt mt-3 text-xl text-muted">Ribuan judul dari penulis dan penerbit mandiri.</p>
                </div>
                <Link :href="route('catalog.index')" class="lk text-sm text-accent hover:underline">
                    Lihat semua kategori →
                </Link>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <Link
                    v-for="cat in popularCategories"
                    :key="cat.id"
                    :href="route('catalog.index', { category: cat.slug })"
                    class="group card-soft flex flex-col items-center gap-4 rounded-md px-4 py-8 text-center transition-all hover:-translate-y-1 hover:bg-ink hover:text-white"
                >
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-ink text-white transition-colors group-hover:bg-white group-hover:text-ink">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20V4H6.5A2.5 2.5 0 004 6.5v13z" />
                            <path d="M4 19.5A2.5 2.5 0 006.5 22H20v-5" />
                        </svg>
                    </span>
                    <span class="lk text-lg">{{ cat.name }}</span>
                    <span class="text-sm text-muted transition-colors group-hover:text-faint">{{ cat.books_count }} buku</span>
                </Link>
            </div>
        </section>

        <!-- KATALOG -->
        <section class="mx-auto max-w-6xl px-5 pt-28">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <h2 class="h text-4xl md:text-[44px]">Pilihan untuk dibaca hari ini.</h2>
                    <p class="lt mt-3 text-xl text-muted">Satu buku gratis setiap bulan, dipilih tim kami.</p>
                </div>
                <div class="inline-flex rounded-full bg-soft p-1" role="tablist">
                    <button
                        role="tab"
                        class="lk h-10 rounded-full px-5 text-sm"
                        :class="catalogTab === 'top' ? 'seg-on' : 'seg-off'"
                        :aria-selected="catalogTab === 'top'"
                        @click="catalogTab = 'top'"
                    >
                        Terlaris
                    </button>
                    <button
                        role="tab"
                        class="lk h-10 rounded-full px-5 text-sm"
                        :class="catalogTab === 'new' ? 'seg-on' : 'seg-off'"
                        :aria-selected="catalogTab === 'new'"
                        @click="catalogTab = 'new'"
                    >
                        Terbaru
                    </button>
                </div>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4">
                <BookCard v-for="book in books" :key="book.id" :book="book" />
            </div>
            <div class="mt-10 text-center">
                <Link :href="route('catalog.index')" class="btn-outline">Lihat semua buku</Link>
            </div>
        </section>

        <!-- KEUNGGULAN + STATISTIK -->
        <section class="mx-auto max-w-6xl px-5 pt-28">
            <h2 class="h max-w-2xl text-4xl md:text-[44px]">
                Dibuat agar pembaca betah dan penulis dibayar adil.
            </h2>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <article
                    v-for="(feature, i) in features"
                    :key="feature.title"
                    class="card flex flex-col transition-all hover:-translate-y-1 hover:shadow-md"
                    :class="{ '!bg-ink !text-white [&_.feat-desc]:!text-faint': i === 1 }"
                >
                    <span class="lk mb-4 grid h-10 w-10 place-items-center rounded-full"
                        :class="i === 1 ? 'bg-accent text-white' : 'bg-soft text-ink'"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path v-if="i === 0" d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" />
                            <path v-else-if="i === 1" d="M12 2v20 M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                            <path v-else d="M12 1v22 M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                        </svg>
                    </span>
                    <h3 class="h text-2xl">{{ feature.title }}</h3>
                    <p class="feat-desc mt-3 text-muted">{{ feature.desc }}</p>
                </article>
            </div>
            <dl class="mt-16 grid grid-cols-3 gap-4 rounded-md bg-soft py-10 text-center">
                <div v-for="stat in statsList" :key="stat.label">
                    <dd class="h text-4xl md:text-[56px]">{{ stat.value }}</dd>
                    <dt class="mt-2 text-muted">{{ stat.label }}</dt>
                </div>
            </dl>
        </section>

        <!-- PENULIS & PENERBIT -->
        <section id="penulis" class="mx-auto max-w-5xl px-5 pt-28">
            <div class="text-center">
                <h2 class="h text-4xl md:text-[44px]">Terbitkan bukumu hari ini.</h2>
                <p class="lt mx-auto mt-3 max-w-lg text-xl text-muted">
                    Akun penulis dan penerbit langsung aktif saat mendaftar, tanpa menunggu verifikasi.
                </p>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-2">
                <article class="card flex flex-col">
                    <h3 class="h text-2xl">Penulis</h3>
                    <p class="mt-2 text-muted">Untuk kamu yang ingin menjual karya sendiri.</p>
                    <ul class="mt-5 flex-1 space-y-3 text-sm text-muted">
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Unggah EPUB atau PDF dan cover dari browser
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Atur diskon per buku dengan jadwal sendiri
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Pantau penjualan dan royalti, ekspor ke CSV
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Tarik dana langsung dari dashboard
                        </li>
                    </ul>
                    <Link :href="route('register')" class="btn-outline mt-6">Daftar sebagai penulis</Link>
                </article>
                <article class="card-soft flex flex-col">
                    <div class="flex items-center gap-3">
                        <h3 class="h text-2xl">Penerbit</h3>
                        <span class="badge-accent">Bagi hasil</span>
                    </div>
                    <p class="mt-2 text-muted">Semua fitur penulis, ditambah kemitraan dan voucher.</p>
                    <ul class="mt-5 flex-1 space-y-3 text-sm text-muted">
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Semua fitur penulis
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Ajukan kemitraan dengan penulis, bagi hasil per buku
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Buat voucher untuk buku imprint dan mitra
                        </li>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7" /></svg>
                            Tandai buku mitra sebagai unggulan
                        </li>
                    </ul>
                    <Link :href="route('register')" class="btn-primary mt-6">Daftar sebagai penerbit</Link>
                </article>
            </div>
        </section>

        <!-- TESTIMONI -->
        <section class="mx-auto max-w-6xl px-5 pt-28">
            <h2 class="h max-w-xl text-4xl md:text-[44px]">Kata mereka yang sudah mencoba.</h2>
            <div class="mt-10 columns-1 gap-5 sm:columns-2 lg:columns-4">
                <figure
                    v-for="t in testimonials"
                    :key="t.name"
                    class="card mb-5 break-inside-avoid rounded-sm"
                >
                    <div class="flex items-center gap-3">
                        <span class="lk grid h-10 w-10 place-items-center rounded-full bg-soft text-sm text-ink">{{ t.name.charAt(0) }}</span>
                        <div>
                            <p class="lk">{{ t.name }}</p>
                            <p class="text-sm text-muted">{{ t.role }}</p>
                        </div>
                    </div>
                    <blockquote class="mt-4 text-sm text-muted">
                        {{ t.quote }}
                    </blockquote>
                </figure>
            </div>
            <p class="mt-4 text-xs text-faint">Testimoni di atas adalah contoh, ganti dengan ulasan asli sebelum rilis.</p>
        </section>

        <!-- CTA + NEWSLETTER -->
        <section class="mx-auto max-w-6xl px-5 py-28">
            <div class="rounded-md bg-ink px-6 py-16 text-center text-white md:py-24">
                <h2 class="h mx-auto max-w-2xl text-4xl md:text-[56px]">Mulai membaca malam ini.</h2>
                <p class="lt mx-auto mt-4 max-w-xl text-xl text-faint">
                    Daftar gratis, lalu simpan wishlist dan dapatkan kabar flash sale pertamamu.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link :href="route('register')" class="btn-primary !bg-white !text-ink hover:!bg-soft">Daftar gratis</Link>
                    <Link :href="route('login')" class="btn-outline !border-white/30 !bg-transparent !text-white hover:!bg-white/10">Masuk</Link>
                </div>
                <form class="mx-auto mt-12 flex max-w-md flex-col gap-3 sm:flex-row" novalidate @submit.prevent="subscribe">
                    <label class="sr-only" for="nlEmail">Alamat email</label>
                    <input
                        id="nlEmail"
                        v-model="nlEmail"
                        type="email"
                        placeholder="Email kamu"
                        class="field h-12 flex-1 !bg-white/10 !text-white placeholder:!text-faint"
                    />
                    <button type="submit" class="btn-primary !bg-white !text-ink hover:!bg-soft">Berlangganan</button>
                </form>
                <p class="mt-3 min-h-5 text-sm text-faint" aria-live="polite">{{ nlMsg }}</p>
            </div>
        </section>
    </main>

    <AppFooter />
</template>
