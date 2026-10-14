<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
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

const nlEmail = ref('');
const nlMsg = ref('Kabar promo dan buku baru, sebulan sekali.');

function subscribe() {
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nlEmail.value.trim());
    nlMsg.value = ok
        ? 'Terima kasih, kamu sudah terdaftar.'
        : 'Masukkan alamat email yang valid, misalnya nama@email.com.';
    if (ok) nlEmail.value = '';
}

const categoryIcons = ['auto_stories', 'trending_up', 'account_balance', 'psychology', 'code', 'child_care'];

const features = [
    {
        icon: 'chrome_reader_mode',
        title: 'Reader yang nyaman.',
        desc: 'EPUB dan PDF di browser, progres baca lintas perangkat otomatis, bookmark, highlight kutipan favorit, tema terang/sepia/gelap yang ramah mata.',
        stat: 'Sinkronisasi instan ponsel & laptop',
        statIcon: 'sync',
    },
    {
        icon: 'redeem',
        title: 'Voucher dan poin.',
        desc: 'Dapatkan poin reward di setiap transaksi pembelian, tukar poin dengan diskon tambahan saat checkout, plus voucher musiman.',
        stat: '1.000 Poin = Diskon Rp10.000',
        statIcon: 'loyalty',
    },
    {
        icon: 'payments',
        title: 'Royalti transparan.',
        desc: 'Laporan ledger tercatat per transaksi secara real-time, transparansi persentase, dan tarik dana langsung ke rekening bank dari dashboard.',
        stat: 'Pencairan harian tanpa potongan gelap',
        statIcon: 'verified',
    },
];

const testimonials = [
    { initials: 'TS', name: 'Tania Siregar', role: 'Pembaca', quote: 'Reader web-nya paling bersih dan responsif dibanding aplikasi buku lokal lain. Buka naskah 600 halaman tidak pernah lag.' },
    { initials: 'AW', name: 'Arya Wirawan', role: 'Penulis', quote: 'Sebagai penulis independen, royalti yang masuk tiap jam dan bisa ditarik tanpa minimum adalah mimpi yang jadi kenyataan.' },
    { initials: 'MP', name: 'Matapena Press', role: 'Penerbit', quote: 'Split royalty multi-kontributor memudahkan tim kami membayar ilustrator dan editor tanpa rekap Excel berulang setiap bulan.' },
    { initials: 'RP', name: 'Rian Pratama', role: 'Pembaca', quote: 'Mode sepianya sangat nyaman untuk membaca maraton di laptop saat malam. Fitur highlight kutipannya juga rapi.' },
];

const statsList = computed(() => [
    { value: Number(props.stats.total_books).toLocaleString('id-ID'), label: 'Buku tersedia' },
    { value: Number(props.stats.total_authors).toLocaleString('id-ID'), label: 'Penulis dan penerbit' },
    { value: Number(props.stats.total_readers).toLocaleString('id-ID'), label: 'Pembaca aktif' },
]);

function discountPercent(book) {
    if (!book.original_price || !book.price) return 0;
    return Math.round(((book.original_price - book.price) / book.original_price) * 100);
}

function soldPercent(book) {
    if (!book.stock || !book.sold_count) return 0;
    return Math.min(100, Math.round((book.sold_count / book.stock) * 100));
}
</script>

<template>
    <Head title="PerpusKita — Baca, beli, dan terbitkan buku digital" />

    <AppHeader />

    <main>
        <!-- HERO -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg pt-16 pb-20 text-center">
            <div class="mx-auto flex max-w-4xl flex-col items-center">
                <h1 class="text-display text-ink font-semibold tracking-tight">
                    Baca, beli, dan terbitkan buku. Semuanya di satu tempat.
                </h1>
                <p class="text-body-lead text-muted max-w-2xl mx-auto mt-6">
                    Toko buku digital dengan reader EPUB dan PDF langsung di browser, promo yang rutin datang, dan bagi hasil yang jelas untuk penulis dan penerbit.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
                    <Link :href="route('catalog.index')" class="btn-primary !px-8 !py-3.5">Mulai membaca</Link>
                    <Link :href="route('register')" class="btn-outline !px-8 !py-3.5">Daftar penulis</Link>
                    <a href="#penulis" class="btn-soft !px-8 !py-3.5">Jadi penerbit ↗</a>
                </div>
            </div>
        </section>

        <!-- MARQUEE COVER BUKU -->
        <section class="w-full overflow-hidden py-6 bg-canvas" aria-label="Buku pilihan">
            <div class="marquee flex w-max gap-6 pr-6">
                <Link
                    v-for="(book, i) in [...marqueeBooks, ...marqueeBooks]"
                    :key="i"
                    :href="route('catalog.show', book.slug)"
                    class="w-52 shrink-0 group cursor-pointer"
                >
                    <div class="w-full aspect-[3/4] rounded-2xl overflow-hidden bg-field relative">
                        <img
                            v-if="book.cover_url"
                            :src="book.cover_url"
                            :alt="book.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        />
                        <CoverImage v-else :book="book" />
                    </div>
                    <p class="text-title-md text-ink mt-3 truncate font-semibold">{{ book.title }}</p>
                    <p class="text-caption text-muted">{{ book.author_name }}</p>
                </Link>
            </div>
        </section>

        <!-- FLASH SALE -->
        <section
            v-if="flashSale && flashSale.items && flashSale.items.length"
            class="mx-auto w-full max-w-[1440px] px-margin-lg py-20"
        >
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-10">
                <div>
                    <div class="badge-accent mb-3 inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">bolt</span>
                        <span>Flash sale</span>
                    </div>
                    <h2 class="text-headline-sm text-ink font-semibold tracking-tight">
                        {{ flashSale.name || 'Harga turun, waktunya terbatas.' }}
                    </h2>
                    <p v-if="flashSale.banner_text" class="text-body-md text-muted mt-1">
                        {{ flashSale.banner_text }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-soft rounded-2xl p-4 w-20 text-center">
                        <span class="text-title-lg text-ink font-semibold block leading-none">{{ flashSale.hours || '04' }}</span>
                        <span class="text-caption text-muted mt-1 block">Jam</span>
                    </div>
                    <div class="bg-soft rounded-2xl p-4 w-20 text-center">
                        <span class="text-title-lg text-ink font-semibold block leading-none">{{ flashSale.minutes || '38' }}</span>
                        <span class="text-caption text-muted mt-1 block">Menit</span>
                    </div>
                    <div class="bg-soft rounded-2xl p-4 w-20 text-center">
                        <span class="text-title-lg text-ink font-semibold block leading-none">{{ flashSale.seconds || '19' }}</span>
                        <span class="text-caption text-muted mt-1 block">Detik</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div v-for="item in flashSale.items.slice(0, 5)" :key="item.id" class="group flex flex-col cursor-pointer">
                    <div class="w-full aspect-[3/4] rounded-2xl overflow-hidden bg-field relative mb-3">
                        <img
                            v-if="item.book.cover_url"
                            :src="item.book.cover_url"
                            :alt="item.book.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        />
                        <CoverImage v-else :book="item.book" />
                        <div v-if="discountPercent(item.book) > 0" class="absolute top-2.5 left-2.5 bg-scrim text-white px-2.5 py-1 rounded-full text-badge-label">
                            Diskon {{ discountPercent(item.book) }}%
                        </div>
                        <button
                            type="button"
                            aria-label="Simpan ke wishlist"
                            class="absolute top-2.5 right-2.5 w-9 h-9 bg-canvas rounded-full flex items-center justify-center text-ink hover:text-error transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">favorite</span>
                        </button>
                    </div>
                    <h3 class="text-title-md text-ink truncate font-semibold">{{ item.book.title }}</h3>
                    <p class="text-body-sm text-muted mt-0.5">{{ item.book.author_name }} • ★ {{ item.book.average_rating }}</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-action text-ink font-semibold">Rp{{ Number(item.book.price).toLocaleString('id-ID') }}</span>
                        <span v-if="item.book.original_price && item.book.original_price > item.book.price" class="text-caption text-faint line-through">
                            Rp{{ Number(item.book.original_price).toLocaleString('id-ID') }}
                        </span>
                    </div>
                    <div class="w-full bg-field h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-ink h-full rounded-full" :style="{ width: soldPercent(item.book) + '%' }"></div>
                    </div>
                    <span class="text-caption text-muted mt-1.5 block">Terjual {{ soldPercent(item.book) }}%</span>
                </div>
            </div>
        </section>

        <!-- KATEGORI POPULER -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg py-16">
            <h2 class="text-headline-sm text-ink font-semibold tracking-tight mb-8">
                Jelajahi menurut kategori.
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <Link
                    v-for="(cat, i) in popularCategories"
                    :key="cat.id"
                    :href="route('catalog.index', { category: cat.slug })"
                    class="bg-soft rounded-[24px] p-6 hover:bg-field transition-colors flex flex-col justify-between group"
                >
                    <div class="w-14 h-14 bg-canvas rounded-[30%] flex items-center justify-center text-ink mb-4">
                        <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">{{ categoryIcons[i % categoryIcons.length] }}</span>
                    </div>
                    <div>
                        <h3 class="text-title-md text-ink font-semibold">{{ cat.name }}</h3>
                        <p class="text-body-sm text-muted mt-1">{{ cat.books?.length || 0 }} buku</p>
                    </div>
                </Link>
            </div>
        </section>

        <!-- KATALOG HARIAN -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg py-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                <h2 class="text-headline-sm text-ink font-semibold tracking-tight">
                    Pilihan untuk dibaca hari ini.
                </h2>
                <div class="bg-soft p-1 rounded-full flex gap-1 self-start sm:self-auto" role="tablist">
                    <button
                        role="tab"
                        class="lk px-5 py-2 rounded-full text-action"
                        :class="catalogTab === 'top' ? 'seg-on' : 'seg-off'"
                        :aria-selected="catalogTab === 'top'"
                        @click="catalogTab = 'top'"
                    >
                        Terlaris
                    </button>
                    <button
                        role="tab"
                        class="lk px-5 py-2 rounded-full text-action"
                        :class="catalogTab === 'new' ? 'seg-on' : 'seg-off'"
                        :aria-selected="catalogTab === 'new'"
                        @click="catalogTab = 'new'"
                    >
                        Terbaru
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div v-for="book in books.slice(0, 8)" :key="book.id" class="group flex flex-col cursor-pointer">
                    <div class="w-full aspect-[3/4] rounded-2xl overflow-hidden bg-field relative mb-3">
                        <img
                            v-if="book.cover_url"
                            :src="book.cover_url"
                            :alt="book.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        />
                        <CoverImage v-else :book="book" />
                        <div v-if="book.is_free" class="absolute top-2.5 left-2.5 bg-accent text-white px-2.5 py-1 rounded-full text-badge-label">
                            Buku bulan ini
                        </div>
                        <button
                            type="button"
                            aria-label="Wishlist"
                            class="absolute top-2.5 right-2.5 w-9 h-9 bg-canvas rounded-full flex items-center justify-center text-ink hover:text-error transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">favorite</span>
                        </button>
                    </div>
                    <h3 class="text-title-md text-ink truncate font-semibold">{{ book.title }}</h3>
                    <p class="text-body-sm text-muted mt-0.5">{{ book.author_name }} • ★ {{ book.average_rating }}</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span v-if="book.is_free" class="text-action text-accent font-semibold">Gratis</span>
                        <span v-else class="text-action text-ink font-semibold">Rp{{ Number(book.price).toLocaleString('id-ID') }}</span>
                        <span v-if="book.original_price && book.original_price > book.price && !book.is_free" class="text-caption text-faint line-through">
                            Rp{{ Number(book.original_price).toLocaleString('id-ID') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <Link :href="route('catalog.index')" class="btn-outline !px-8 !py-3.5">Lihat semua buku</Link>
            </div>
        </section>

        <!-- KEUNGGULAN -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg py-20">
            <h2 class="text-headline-md text-ink font-semibold tracking-tight text-center max-w-3xl mx-auto mb-12">
                Dibuat agar pembaca betah dan penulis dibayar adil.
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <article
                    v-for="(feature, i) in features"
                    :key="feature.title"
                    class="rounded-[32px] p-8 flex flex-col justify-between"
                    :class="i === 1 ? 'bg-canvas border border-hair' : 'bg-soft'"
                >
                    <div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-ink mb-6" :class="i === 1 ? 'bg-field' : 'bg-canvas'">
                            <span class="material-symbols-outlined text-[24px]">{{ feature.icon }}</span>
                        </div>
                        <h3 class="text-title-lg text-ink font-semibold">{{ feature.title }}</h3>
                        <p class="text-body-md text-muted mt-4">{{ feature.desc }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-hairline-soft flex items-center gap-3 text-ink">
                        <span class="material-symbols-outlined text-[20px]">{{ feature.statIcon }}</span>
                        <span class="text-caption text-muted">{{ feature.stat }}</span>
                    </div>
                </article>
            </div>
            <div class="border-t border-hairline-soft pt-12 mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div v-for="stat in statsList" :key="stat.label">
                    <span class="text-headline-lg text-ink font-semibold block tracking-tight">{{ stat.value }}</span>
                    <span class="text-body-md text-muted mt-2 block">{{ stat.label }}</span>
                </div>
            </div>
        </section>

        <!-- UNTUK PENULIS DAN PENERBIT -->
        <section id="penulis" class="mx-auto w-full max-w-[1440px] px-margin-lg py-20">
            <div class="max-w-2xl mb-12">
                <h2 class="text-headline-md text-ink font-semibold tracking-tight">
                    Terbitkan bukumu hari ini.
                </h2>
                <p class="text-body-lead text-muted mt-3">
                    Akun penulis dan penerbit langsung aktif saat mendaftar, tanpa menunggu verifikasi.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <article class="bg-canvas border border-hair rounded-[32px] p-10 flex flex-col justify-between">
                    <div>
                        <span class="text-caption uppercase tracking-wider text-muted block mb-2">Pilihan Mandiri</span>
                        <h3 class="text-headline-sm text-ink font-semibold">Penulis</h3>
                        <p class="text-body-md text-muted mt-2 mb-8">
                            Kendalikan hak cipta karya, format buku, serta jadwal rilis secara independen.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Unggah naskah EPUB/PDF dan desain cover</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Atur diskon promo per buku secara leluasa</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Pantau grafik penjualan dan royalti harian</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Tarik dana royalti kapan saja tanpa batas minimum berbelit</span>
                            </li>
                        </ul>
                    </div>
                    <Link :href="route('register')" class="btn-outline w-full text-center !px-8 !py-3.5 mt-10">Daftar sebagai penulis</Link>
                </article>
                <article class="bg-soft rounded-[32px] p-10 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-caption uppercase tracking-wider text-muted block">Organisasi & Studio</span>
                            <span class="badge-accent">Bagi hasil</span>
                        </div>
                        <h3 class="text-headline-sm text-ink font-semibold">Penerbit</h3>
                        <p class="text-body-md text-muted mt-2 mb-8">
                            Solusi korporasi penerbitan skala besar dengan pembagian multi-penulis terautomasi.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Semua fitur akun penulis</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Manajemen kemitraan & split bagi hasil multi-penulis per buku</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Terbitkan voucher khusus buku imprint penerbit</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-ink text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <span class="text-body-md text-ink">Tandai buku mitra pilihan sebagai unggulan katalog</span>
                            </li>
                        </ul>
                    </div>
                    <Link :href="route('register')" class="btn-primary w-full text-center !px-8 !py-3.5 mt-10">Daftar sebagai penerbit</Link>
                </article>
            </div>
        </section>

        <!-- TESTIMONI -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg py-16">
            <h2 class="text-headline-sm text-ink font-semibold tracking-tight mb-8">
                Kata mereka yang sudah mencoba.
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <figure
                    v-for="t in testimonials"
                    :key="t.name"
                    class="bg-canvas border border-hairline-soft rounded-2xl p-6 flex flex-col justify-between"
                >
                    <blockquote class="text-body-md text-ink">
                        "{{ t.quote }}"
                    </blockquote>
                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-hairline-soft">
                        <div class="w-10 h-10 rounded-full bg-soft text-ink font-semibold flex items-center justify-center shrink-0 text-sm">
                            {{ t.initials }}
                        </div>
                        <div class="truncate">
                            <h4 class="text-action text-ink truncate font-semibold">{{ t.name }}</h4>
                            <span class="text-caption text-muted">{{ t.role }}</span>
                        </div>
                    </div>
                </figure>
            </div>
        </section>

        <!-- CTA PENUTUP -->
        <section class="mx-auto w-full max-w-[1440px] px-margin-lg py-20 text-center">
            <div class="mx-auto flex max-w-2xl flex-col items-center">
                <h2 class="text-headline-lg text-ink font-semibold tracking-tight">
                    Mulai membaca malam ini.
                </h2>
                <p class="text-body-lead text-muted mt-4">
                    Daftar gratis dalam 30 detik dan nikmati akses koleksi buku digital Indonesia terlengkap langsung dari peramban Anda.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
                    <Link :href="route('register')" class="btn-primary !px-8 !py-3.5">Daftar gratis</Link>
                    <Link :href="route('login')" class="btn-outline !px-8 !py-3.5">Masuk</Link>
                </div>
                <div class="w-full max-w-md mt-14">
                    <form class="flex items-center bg-field rounded-full p-1.5 focus-within:ring-2 focus-within:ring-ink transition-all" novalidate @submit.prevent="subscribe">
                        <label class="sr-only" for="nlEmail">Alamat email</label>
                        <input
                            id="nlEmail"
                            v-model="nlEmail"
                            type="email"
                            placeholder="Email kamu"
                            required
                            class="bg-transparent border-none text-ink placeholder:text-faint px-5 py-2.5 w-full focus:outline-none text-body-md"
                        />
                        <button type="submit" class="btn-primary shrink-0 !px-6 !py-2.5">Berlangganan</button>
                    </form>
                    <span class="text-caption text-muted mt-3 block">
                        {{ nlMsg }}
                    </span>
                </div>
            </div>
        </section>
    </main>

    <AppFooter />
</template>
