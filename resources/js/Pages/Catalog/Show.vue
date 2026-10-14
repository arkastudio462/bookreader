<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import CoverImage from '@/Components/CoverImage.vue';
import FlashSaleCountdown from '@/Components/FlashSaleCountdown.vue';

const props = defineProps({
    book: Object,
    relatedBooks: Array,
    accessMode: { type: String, default: null },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const flashError = computed(() => page.props.flash?.error ?? null);
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const effectivePrice = computed(() => Number(props.book.effective_price ?? props.book.price));
const basePrice = computed(() => Number(props.book.base_price ?? props.book.price));
const formatNumber = (n) => Number(n).toLocaleString('id-ID');
const discountPercent = computed(() => {
    if (!basePrice.value || !effectivePrice.value) return 0;
    return Math.round(((basePrice.value - effectivePrice.value) / basePrice.value) * 100);
});

const activeTab = ref('synopsis');
const readerFontSize = ref(16);
const readerTheme = ref('white');
const licenseMode = ref('buy');

function addToWishlist() {
    router.post(route('wishlist.store'), { book_id: props.book.id }, { preserveScroll: true });
}

function addToCart() {
    router.post(route('cart.store'), { book_id: props.book.id }, { preserveScroll: true });
}

const metaItems = computed(() => {
    const items = [];
    if (props.book.pages) items.push({ label: 'Ketebalan Buku', value: props.book.pages + ' Halaman', icon: 'auto_stories' });
    items.push({ label: 'Format Berkas', value: 'EPUB 3 & PDF', icon: 'extension' });
    items.push({ label: 'Ukuran Unduhan', value: '14.2 MB', icon: 'cloud_download' });
    items.push({ label: 'Lisensi Pembaca', value: 'Multi-Device Offline', icon: 'devices_wearables' });
    return items;
});

const highlights = [
    'Riset arsip sejarah kolonial Hindia Belanda kurun waktu 1930–1940.',
    'Dilengkapi 8 ilustrasi peta arsitektural kota lama tempo dulu.',
    'Bahasa puitis bernas tanpa mengorbankan dinamisme alur cerita.',
    'Terpilih sebagai Buku Fiksi Terbaik Pilihan Editor PerpusKita 2023.',
];

const reviews = [
    { initials: 'DN', name: 'Dimas Nugraha', badge: 'Pembeli Terverifikasi', stars: 5, text: 'Pengalaman membaca terbaik di platform PerpusKita sejauh ini. Suasana hujan di Kota Lama digambarkan dengan sangat atmosferik. Fitur web reader dengan mode sepia membuat saya betah maraton 5 jam tanpa lelah.', date: '12 November 2023', format: 'Format EPUB' },
    { initials: 'SL', name: 'Siti Lestari', badge: 'Pembeli Terverifikasi', stars: 5, text: 'Riset sejarahnya luar biasa presisi. Karakter Maria van den Berg sangat kompleks dan hidup. Buku ini wajib masuk daftar bacaan siapa pun yang mencintai sejarah Indonesia tempo dulu.', date: '28 Oktober 2023', format: 'Format PDF Interaktif' },
    { initials: 'RH', name: 'Rian Hidayat', badge: 'Pembaca Terverifikasi', stars: 4, text: 'Alurnya rapi dan endingnya sangat membekas. Sinkronisasi dari laptop ke handphone bekerja seketika tanpa hambatan. Layak mendapatkan penghargaan tahun ini.', date: '04 November 2023', format: 'Format EPUB' },
];

const primaryAction = computed(() => {
    if (props.accessMode === 'full' || props.accessMode === 'sample') {
        return { type: 'link', href: route('reader.show', props.book.id), label: props.accessMode === 'sample' ? 'Baca sampel gratis' : 'Baca buku' };
    }
    if (props.user) {
        return { type: 'button', label: 'Tambah ke keranjang' };
    }
    return { type: 'link', href: route('login'), label: 'Beli sekarang' };
});

const secondaryAction = computed(() => {
    if (props.accessMode === 'sample') return null;
    if (props.user) {
        return { type: 'button', label: '+ Wishlist' };
    }
    return { type: 'link', href: route('login'), label: '+ Wishlist' };
});
</script>

<template>
    <Head :title="book.title" />

    <div class="flex min-h-screen flex-col bg-canvas">
        <AppHeader />

        <main class="pb-space-xl">
            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pt-space-md pb-space-lg">
                <div class="flex flex-wrap items-center justify-between gap-space-md">
                    <nav class="flex items-center gap-space-xs text-body-sm text-muted" aria-label="Breadcrumb">
                        <Link :href="route('catalog.index')" class="transition-colors hover:text-ink">Katalog</Link>
                        <span class="text-faint">/</span>
                        <Link v-if="book.category" :href="route('catalog.index', { category: book.category.slug })" class="transition-colors hover:text-ink">{{ book.category.name }}</Link>
                        <template v-if="book.category"><span class="text-faint">/</span></template>
                        <span class="font-semibold text-ink">{{ book.title }}</span>
                    </nav>
                    <div class="flex items-center gap-space-sm">
                        <div class="flex items-center gap-space-xs rounded-full bg-soft px-space-md py-space-xs text-body-sm text-muted">
                            <span class="h-2 w-2 rounded-full bg-accent"></span>
                            <span>Edisi Digital Terverifikasi</span>
                        </div>
                        <button type="button" aria-label="Bagikan buku" class="grid h-10 w-10 place-items-center rounded-full bg-soft text-ink transition-colors hover:bg-field">
                            <span class="material-symbols-outlined text-[20px]">share</span>
                        </button>
                    </div>
                </div>
            </section>

            <section v-if="flashError" class="card-soft mx-auto mt-4 max-w-[1440px] px-margin-lg text-sm text-ink">{{ flashError }}</section>
            <section v-if="flashSuccess" class="card-soft mx-auto mt-4 max-w-[1440px] px-margin-lg text-sm text-ink">{{ flashSuccess }}</section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="grid grid-cols-1 gap-gutter-lg lg:grid-cols-12">
                    <div class="flex flex-col gap-space-lg lg:col-span-5">
                        <div class="relative flex min-h-[520px] flex-col items-center justify-center rounded-lg bg-soft p-space-xl">
                            <div class="absolute top-space-lg left-space-lg flex items-center gap-space-xs rounded-full bg-ink px-space-md py-space-xs text-badge-label text-white">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                <span>Pilihan Redaksi PerpusKita</span>
                            </div>
                            <div class="absolute top-space-lg right-space-lg rounded-full bg-canvas px-space-sm py-space-xs text-caption text-muted">
                                EPUB 3.0 • PDF
                            </div>
                            <div class="relative mt-4 aspect-[3/4] w-64 overflow-hidden rounded-lg bg-canvas p-2 shadow-sm">
                                <img v-if="book.cover_url" :src="book.cover_url" :alt="book.title" class="h-full w-full rounded-md object-cover" />
                                <CoverImage v-else :book="book" />
                            </div>
                            <div class="mt-space-lg flex flex-wrap items-center justify-center gap-space-xs">
                                <span class="flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-space-xs text-caption text-muted"><span class="material-symbols-outlined text-[14px]">dark_mode</span> Mode Gelap</span>
                                <span class="flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-space-xs text-caption text-muted"><span class="material-symbols-outlined text-[14px]">menu_book</span> {{ book.pages || 320 }} Halaman</span>
                                <span class="flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-space-xs text-caption text-muted"><span class="material-symbols-outlined text-[14px]">timer</span> ~5 jam 40 mnt</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-space-sm sm:grid-cols-4">
                            <Link :href="route('reader.show', book.id)" class="flex h-12 items-center justify-center gap-space-sm rounded-full bg-soft text-ink transition-colors hover:bg-field sm:col-span-3">
                                <span class="material-symbols-outlined text-[20px]">auto_stories</span>
                                <span>Baca Sampel Gratis (Bab 1–3)</span>
                            </Link>
                            <button type="button" aria-label="Simpan ke Wishlist" class="flex h-12 items-center justify-center rounded-full bg-soft text-ink transition-colors hover:bg-field sm:col-span-1" @click="addToWishlist">
                                <span class="material-symbols-outlined text-[22px]">bookmark_add</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-space-md rounded bg-soft p-space-md">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-canvas text-ink">
                                <span class="material-symbols-outlined text-[20px]">devices</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-body-md text-ink">Sinkronisasi Lintas Perangkat</span>
                                <span class="text-body-sm text-muted">Progres baca, kutipan tersimpan, & catatan otomatis terupdate di semua perangkat.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-lg lg:col-span-7">
                        <div class="flex flex-wrap items-center gap-space-sm">
                            <span v-if="book.category" class="rounded-full bg-soft px-space-md py-space-xs text-badge-label text-ink">{{ book.category.name }}</span>
                            <span v-if="book.on_promo" class="flex items-center gap-space-xs rounded-full bg-accent px-space-md py-space-xs text-badge-label text-white">
                                <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                                <span>Diskon {{ discountPercent }}% Terbatas</span>
                            </span>
                            <span class="text-caption text-faint">ISBN: {{ book.isbn }}</span>
                        </div>

                        <h1 class="text-headline-md font-semibold tracking-tight text-ink">{{ book.title }}</h1>

                        <div class="flex flex-wrap items-center justify-between gap-space-md pb-space-xs">
                            <div class="flex items-center gap-space-sm">
                                <div class="h-11 w-11 shrink-0 overflow-hidden rounded-full bg-field">
                                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDS2VuhpzpTG62ko6ddBHVD3yDsOFuWyoaMzTs9eDDQT-obPv5E9SyXmbRQj-fjmaEG1KVq2x45PN4A3Aq8n5pjgfTLOSF3Rf6DqE9_g0jsroQ_lZEtBCxu9MXfeGlflwtBmWdIOrsXLV7nG6oJd1bL_3IISFlxMciiW68CY28aJGGOWYBAZpihWonhk-MSbGwQ311OBfYlfixbIU0wgQP1K6ZHXO8c-28vxYWuMvJUDaU2-hIJV9KmMg" alt="Author" class="h-full w-full object-cover" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-space-xs">
                                        <span class="text-body-md font-semibold text-ink">{{ book.author_name }}</span>
                                        <span class="material-symbols-outlined text-accent text-[17px]" title="Penulis Terverifikasi">verified</span>
                                    </div>
                                    <span class="text-body-sm text-muted">Diterbitkan oleh <strong class="font-medium text-ink">Matapena Press</strong></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-space-sm rounded-full bg-soft px-space-md py-space-xs">
                                <div class="flex items-center gap-space-xs text-ink">
                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-action">{{ book.average_rating }}</span>
                                </div>
                                <span class="text-faint">•</span>
                                <span class="text-body-sm text-muted">{{ formatNumber(book.rating_count) }} ulasan</span>
                                <span class="text-faint">•</span>
                                <span class="text-body-sm text-muted">{{ formatNumber(book.sold_count || 0) }} pembaca</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-space-md rounded-lg bg-soft p-space-lg">
                            <div class="grid grid-cols-2 gap-1 rounded-full bg-field p-1 text-center text-action">
                                <button type="button" class="rounded-full py-space-xs transition-all" :class="licenseMode === 'buy' ? 'bg-canvas font-semibold text-ink shadow-sm' : 'text-muted hover:text-ink'" @click="licenseMode = 'buy'">Beli Digital Selamanya</button>
                                <button type="button" class="rounded-full py-space-xs transition-all" :class="licenseMode === 'rent' ? 'bg-canvas font-semibold text-ink shadow-sm' : 'text-muted hover:text-ink'" @click="licenseMode = 'rent'">Sewa 30 Hari</button>
                            </div>

                            <div v-if="book.discount_source === 'flash' && book.flash_ends_at" class="rounded bg-canvas p-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="text-sm font-semibold text-ink">Berakhir dalam</span>
                                    <div class="[&_div]:!bg-soft [&_div]:!text-ink">
                                        <FlashSaleCountdown :ends-at="book.flash_ends_at" />
                                    </div>
                                    <span v-if="book.flash_sold_percent" class="text-sm text-muted">Terjual {{ book.flash_sold_percent }}%</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-baseline justify-between gap-space-sm pt-space-xs">
                                <div class="flex items-baseline gap-space-sm">
                                    <span class="text-headline-sm font-semibold text-ink">{{ effectivePrice > 0 ? 'Rp' + formatNumber(effectivePrice) : 'Gratis' }}</span>
                                    <span v-if="basePrice > effectivePrice" class="text-body-md text-faint line-through">Rp{{ formatNumber(basePrice) }}</span>
                                    <span v-if="discountPercent > 0" class="rounded-full bg-canvas px-space-sm py-0.5 text-accent text-badge-label">Hemat Rp{{ formatNumber(basePrice - effectivePrice) }}</span>
                                </div>
                                <div class="flex items-center gap-space-xs text-muted text-body-sm">
                                    <span class="material-symbols-outlined text-[18px] text-accent">toll</span>
                                    <span>Dapatkan +{{ formatNumber(Math.floor(effectivePrice / 1000)) }} Poin</span>
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-space-sm pt-space-xs sm:flex-row">
                                <Link v-if="primaryAction.type === 'link'" :href="primaryAction.href" class="flex h-12 w-full items-center justify-center gap-space-sm rounded-full bg-ink text-white transition-opacity hover:opacity-90 sm:flex-1">
                                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                    <span>{{ primaryAction.label }} — {{ effectivePrice > 0 ? 'Rp' + formatNumber(effectivePrice) : 'Gratis' }}</span>
                                </Link>
                                <button v-else type="button" class="flex h-12 w-full items-center justify-center gap-space-sm rounded-full bg-ink text-white transition-opacity hover:opacity-90 sm:flex-1" @click="addToCart">
                                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                    <span>{{ primaryAction.label }}</span>
                                </button>
                                <button v-if="secondaryAction && secondaryAction.type === 'button'" type="button" class="flex h-12 w-full items-center justify-center gap-space-xs rounded-full bg-canvas px-space-xl text-ink transition-colors hover:bg-white sm:w-auto" @click="addToWishlist">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    <span>Wishlist</span>
                                </button>
                                <Link v-else-if="secondaryAction" :href="secondaryAction.href" class="flex h-12 w-full items-center justify-center gap-space-xs rounded-full bg-canvas px-space-xl text-ink transition-colors hover:bg-white sm:w-auto">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    <span>Wishlist</span>
                                </Link>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-y-space-xs pt-space-xs text-muted text-caption">
                                <div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[16px]">check_circle</span><span>Akses instan di browser & aplikasi</span></div>
                                <div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[16px]">security</span><span>Garansi kepuasan baca 7 hari</span></div>
                                <div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[16px]">lock_open</span><span>Bebas DRM untuk cadangan pribadi</span></div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-space-sm rounded-lg bg-soft p-space-lg">
                            <span class="text-badge-label tracking-wider text-muted uppercase">Catatan Kurator Sastra</span>
                            <p class="text-body-lead leading-relaxed text-ink italic">"{{ book.description }}"</p>
                            <div class="flex items-center gap-space-xs pt-space-xs text-body-sm text-muted">
                                <span>Kurasi oleh</span>
                                <strong class="font-semibold text-ink">Dewan Kurasi Fiksi PerpusKita</strong>
                                <span>• Edisi Mingguan #42</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="grid grid-cols-2 gap-gutter md:grid-cols-4">
                    <div v-for="item in metaItems" :key="item.label" class="flex min-h-[140px] flex-col justify-between rounded bg-soft p-space-lg">
                        <span class="material-symbols-outlined text-ink text-[24px]">{{ item.icon }}</span>
                        <div class="flex flex-col">
                            <span class="text-caption text-muted">{{ item.label }}</span>
                            <span class="text-title-md text-ink">{{ item.value }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="flex flex-col gap-space-lg rounded-lg bg-soft p-space-xl">
                    <div class="flex flex-col justify-between gap-space-md md:flex-row md:items-center">
                        <div>
                            <div class="mb-space-xs flex items-center gap-space-xs text-accent text-badge-label uppercase tracking-wider">
                                <span class="h-2 w-2 rounded-full bg-accent"></span>
                                Simulasi Web Reader
                            </div>
                            <h2 class="text-title-lg font-semibold text-ink">Pratinjau Pengalaman Membaca</h2>
                        </div>
                        <div class="flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-space-xs shadow-sm">
                            <div class="flex items-center gap-1 px-space-xs text-ink">
                                <button type="button" class="grid h-8 w-8 place-items-center rounded-full text-caption font-semibold hover:bg-soft" @click="readerFontSize = Math.max(12, readerFontSize - 2)">A-</button>
                                <button type="button" class="grid h-8 w-8 place-items-center rounded-full text-action font-semibold hover:bg-soft" @click="readerFontSize = Math.min(24, readerFontSize + 2)">A+</button>
                            </div>
                            <div class="h-5 w-px bg-hair"></div>
                            <div class="flex items-center gap-1.5 px-space-xs">
                                <button type="button" class="h-6 w-6 rounded-full bg-canvas shadow-sm ring-1 ring-ink" title="Mode Putih" @click="readerTheme = 'white'"></button>
                                <button type="button" class="h-6 w-6 rounded-full bg-[#fbf0d9]" title="Mode Sepia" @click="readerTheme = 'sepia'"></button>
                                <button type="button" class="h-6 w-6 rounded-full bg-ink" title="Mode Gelap" @click="readerTheme = 'dark'"></button>
                            </div>
                            <div class="h-5 w-px bg-hair"></div>
                            <span class="px-space-xs text-caption text-muted">Hal. 18 dari {{ book.pages || 320 }} (6%)</span>
                        </div>
                    </div>

                    <div
                        class="mx-auto flex w-full max-w-4xl flex-col gap-space-md rounded-lg bg-canvas p-space-xl shadow-sm"
                        :class="{ '!bg-[#fbf0d9]': readerTheme === 'sepia', '!bg-ink !text-white': readerTheme === 'dark' }"
                    >
                        <div class="pb-space-sm text-center text-caption tracking-widest uppercase" :class="readerTheme === 'dark' ? 'text-faint' : 'text-faint'">Bab I — Suara Gerimis di Heerenstraat</div>
                        <p class="leading-relaxed" :style="{ fontSize: readerFontSize + 'px' }">
                            Malam itu, air bah langit merendam lantai batu bata merah di depan kantor telegraf. Tidak ada andong yang bersedia beranjak melewati jembatan Berok. Suara lonceng gereja Blenduk melantun pelan di kejauhan, teredam deru angin yang membawa bau asin Laut Jawa berbaur dengan rempah gudang tembakau.
                        </p>
                        <p class="leading-relaxed" :style="{ fontSize: readerFontSize + 'px' }">
                            Setyo menutup rapat mantel basahnya. Di dalam kantong kulitnya yang kedap air, ada sepucuk surat bersegel lilin biru tua dari Batavia. Surat yang seharusnya tidak pernah dicatat oleh pegawai arsip residen mana pun.
                        </p>
                        <div class="flex flex-col gap-space-xs pt-space-lg">
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-soft">
                                <div class="h-full w-[6%] rounded-full bg-ink"></div>
                            </div>
                            <div class="flex items-center justify-between text-caption text-faint">
                                <span>Sampel Terbuka: Bab 1</span>
                                <span>Ketuk untuk lanjutkan membaca →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="flex flex-col gap-space-lg">
                    <div class="flex items-center gap-space-xs overflow-x-auto border-b border-hair pb-space-md">
                        <button type="button" class="shrink-0 rounded-full px-space-lg py-space-xs text-action" :class="activeTab === 'synopsis' ? 'bg-ink text-white' : 'bg-soft text-muted hover:text-ink'" @click="activeTab = 'synopsis'">Sinopsis Lengkap</button>
                        <button type="button" class="shrink-0 rounded-full px-space-lg py-space-xs text-action" :class="activeTab === 'toc' ? 'bg-ink text-white' : 'bg-soft text-muted hover:text-ink'" @click="activeTab = 'toc'">Daftar Isi (12 Bab)</button>
                        <button type="button" class="shrink-0 rounded-full px-space-lg py-space-xs text-action" :class="activeTab === 'author' ? 'bg-ink text-white' : 'bg-soft text-muted hover:text-ink'" @click="activeTab = 'author'">Tentang {{ book.author_name }}</button>
                        <button type="button" class="shrink-0 rounded-full px-space-lg py-space-xs text-action" :class="activeTab === 'publisher' ? 'bg-ink text-white' : 'bg-soft text-muted hover:text-ink'" @click="activeTab = 'publisher'">Catatan Penerbit</button>
                    </div>

                    <div class="grid grid-cols-1 gap-gutter-lg lg:grid-cols-12">
                        <div class="flex flex-col gap-space-md text-body-md leading-relaxed text-ink lg:col-span-8">
                            <p>{{ book.description }}</p>
                            <p>Penyelidikannya menuntun pada pertemuan dengan Maria van den Berg, seorang pianis keturunan Indo-Belanda yang mengelola toko buku antik di ujung kawasan Kota Lama. Di balik partitur musik klasik dan buku-buku sastra lawas, Maria menyimpan salinan arsip rahasia pergerakan buruh pelabuhan.</p>
                            <p>Di tengah hujan deras yang seolah tak kunjung reda di atas atap genteng kolonial, cinta dan loyalitas mereka diuji di antara ancaman pembungkaman rezim dan bayang-bayang perang besar yang kian mendekat.</p>
                            <div class="flex flex-wrap gap-space-xs pt-space-md">
                                <span class="rounded-full bg-soft px-space-md py-space-xs text-caption text-muted">#RomanSejarah</span>
                                <span class="rounded-full bg-soft px-space-md py-space-xs text-caption text-muted">#KotaLamaSemarang</span>
                                <span class="rounded-full bg-soft px-space-md py-space-xs text-caption text-muted">#SastraIndonesia</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-space-md rounded-lg bg-soft p-space-lg lg:col-span-4">
                            <span class="text-title-md font-semibold text-ink">Sorotan Utama Buku</span>
                            <ul class="flex flex-col gap-space-sm text-body-sm text-muted">
                                <li v-for="(h, i) in highlights" :key="i" class="flex items-start gap-space-xs">
                                    <span class="material-symbols-outlined shrink-0 text-[18px] text-ink">check</span>
                                    <span>{{ h }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="flex flex-col items-start justify-between gap-space-lg rounded-lg bg-soft p-space-xl md:flex-row md:items-center">
                    <div class="flex flex-col items-start gap-space-lg sm:flex-row sm:items-center">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full bg-field shadow-sm">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbIQs_FZFuQBHsoY-G9fsqVt00ddO27oWRszMfFDxEEUtzyhHIwajj-_Rx7OHk6R_tXaZdimU0VCqkrQRyHIkiAv0HD0W6QCN7Px9xPdgmjtPe2DPp3LwJR3AsD45SGcMn1s1bOAkI-W0KnEG-ZTtveDbQtn-E013EZzndJNAqCoT6EYOAFD2HvIsAyjT8AMmSKLByIHkKl5bKYfKBQFP4F2sucUgvUdUXuMb-IZrmEwMal0phsI5pKQ" alt="Author" class="h-full w-full object-cover" />
                        </div>
                        <div class="flex flex-col gap-space-xs">
                            <div class="flex items-center gap-space-xs">
                                <span class="text-title-lg font-semibold text-ink">{{ book.author_name }}</span>
                                <span class="material-symbols-outlined text-accent text-[20px]" title="Penulis Resmi Terverifikasi">verified</span>
                            </div>
                            <p class="max-w-xl text-body-md text-muted">Sastrawan dan peneliti sejarah urban. Telah menerbitkan 4 novel berlatar sejarah Indonesia abad ke-20 dan memenangkan Kusala Sastra Khatulistiwa.</p>
                            <div class="flex items-center gap-space-md pt-space-xs text-body-sm text-muted">
                                <span><strong>4</strong> Karya</span><span>•</span>
                                <span><strong>38.400+</strong> Pembaca</span><span>•</span>
                                <span><strong>4.92</strong> Rating</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-space-sm">
                        <button type="button" class="h-11 rounded-full bg-ink px-space-lg text-action text-white transition-opacity hover:opacity-90">Ikuti Penulis</button>
                        <button type="button" class="h-11 rounded-full bg-canvas px-space-md text-action text-ink transition-colors hover:bg-white">Lihat Semua Karya</button>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="flex flex-col gap-space-lg">
                    <div class="flex flex-wrap items-end justify-between gap-space-md">
                        <div>
                            <span class="text-badge-label tracking-wider text-muted uppercase">Ulasan & Opini</span>
                            <h2 class="text-headline-sm font-semibold text-ink">Apa Kata Pembaca</h2>
                        </div>
                        <button type="button" class="flex h-11 items-center gap-space-xs rounded-full bg-soft px-space-lg text-action text-ink transition-colors hover:bg-field">
                            <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            <span>Tulis Ulasan Anda</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 items-center gap-gutter rounded-lg bg-soft p-space-lg md:grid-cols-12">
                        <div class="flex flex-col items-center justify-center p-space-md text-center md:col-span-4 md:border-r md:border-b-0 border-b border-hair">
                            <span class="text-display font-semibold text-ink">{{ book.average_rating }}</span>
                            <div class="my-space-xs flex items-center gap-1 text-ink">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                            </div>
                            <span class="text-body-sm text-muted">Berdasarkan {{ formatNumber(book.rating_count) }} ulasan terverifikasi</span>
                        </div>
                        <div class="flex flex-col gap-space-xs px-space-md md:col-span-8">
                            <div v-for="pct in [91, 7, 1.5, 0.3, 0.2]" :key="pct" class="flex items-center gap-space-sm text-body-sm">
                                <span class="w-12 font-medium text-ink">{{ Math.round(pct) }} Bintang</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-canvas"><div class="h-full rounded-full bg-ink" :style="{ width: pct + '%' }"></div></div>
                                <span class="w-10 text-right text-muted">{{ pct }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-gutter md:grid-cols-3">
                        <div v-for="review in reviews" :key="review.name" class="flex flex-col justify-between gap-space-md rounded-lg bg-soft p-space-lg">
                            <div class="flex flex-col gap-space-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-space-sm">
                                        <div class="grid h-9 w-9 place-items-center rounded-full font-semibold text-caption" :class="review.stars >= 5 ? 'bg-ink text-white' : 'bg-field text-ink'">{{ review.initials }}</div>
                                        <div>
                                            <span class="block text-body-md font-semibold text-ink">{{ review.name }}</span>
                                            <span class="text-caption text-muted">{{ review.badge }}</span>
                                        </div>
                                    </div>
                                    <div class="flex text-ink">
                                        <span v-for="s in 5" :key="s" class="material-symbols-outlined text-[16px]" :style="{ 'font-variation-settings': s <= review.stars ? 'FILL 1' : 'FILL 0' }">star</span>
                                    </div>
                                </div>
                                <p class="text-body-md leading-relaxed text-ink">"{{ review.text }}"</p>
                            </div>
                            <span class="text-caption text-faint">{{ review.date }} • {{ review.format }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="relatedBooks.length" class="mx-auto w-full max-w-[1440px] px-margin-lg pb-space-xl">
                <div class="flex flex-col gap-space-lg">
                    <div class="flex flex-wrap items-end justify-between gap-space-md">
                        <div>
                            <span class="text-badge-label tracking-wider text-muted uppercase">Koleksi Terkait</span>
                            <h2 class="text-headline-sm font-semibold text-ink">Pembaca Buku Ini Juga Membaca</h2>
                        </div>
                        <Link v-if="book.category" :href="route('catalog.index', { category: book.category.slug })" class="flex items-center gap-space-xs text-action text-ink hover:underline">
                            <span>Lihat Semua {{ book.category.name }}</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </Link>
                    </div>
                    <div class="grid grid-cols-2 gap-gutter md:grid-cols-4">
                        <Link v-for="b in relatedBooks.slice(0, 4)" :key="b.id" :href="route('catalog.show', b.slug)" class="group flex cursor-pointer flex-col gap-space-sm">
                            <div class="relative aspect-[3/4] overflow-hidden rounded-lg bg-soft p-2 transition-transform duration-300 group-hover:-translate-y-1">
                                <img v-if="b.cover_url" :src="b.cover_url" :alt="b.title" class="h-full w-full rounded-md object-cover" loading="lazy" />
                                <CoverImage v-else :book="b" />
                                <div class="absolute bottom-4 left-4 rounded-full bg-canvas/90 px-space-sm py-0.5 text-badge-label text-ink backdrop-blur-sm">★ {{ b.average_rating }}</div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-body-md font-semibold text-ink line-clamp-1 transition-colors group-hover:text-accent">{{ b.title }}</span>
                                <span class="text-body-sm text-muted">{{ b.author_name }}</span>
                                <div class="mt-space-xs flex items-center justify-between">
                                    <span class="text-action text-ink">Rp{{ formatNumber(b.price) }}</span>
                                    <span class="text-caption text-faint">{{ b.category?.name }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <aside class="pointer-events-none sticky bottom-4 z-40 mx-auto w-full max-w-[1080px] px-margin-lg">
            <div class="pointer-events-auto flex items-center justify-between rounded-full bg-ink px-space-lg py-space-sm text-white shadow-2xl">
                <div class="flex items-center gap-space-md">
                    <div class="hidden flex-col sm:flex">
                        <span class="text-action leading-tight">{{ book.title }}</span>
                        <span class="text-caption text-faint">{{ book.author_name }}</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs">
                        <span class="text-title-md font-semibold">{{ effectivePrice > 0 ? 'Rp' + formatNumber(effectivePrice) : 'Gratis' }}</span>
                        <span v-if="basePrice > effectivePrice" class="text-caption text-faint line-through">Rp{{ formatNumber(basePrice) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-space-xs">
                    <Link :href="route('reader.show', book.id)" class="flex h-10 items-center gap-space-xs rounded-full bg-soft px-space-md text-body-sm text-action text-ink transition-colors hover:bg-white">
                        <span class="material-symbols-outlined text-[18px]">auto_stories</span>
                        <span class="hidden md:inline">Sampel Gratis</span>
                    </Link>
                    <button type="button" class="flex h-10 items-center gap-space-xs rounded-full bg-accent px-space-lg text-body-sm text-action text-white transition-opacity hover:opacity-90" @click="addToCart">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        <span>Beli Digital</span>
                    </button>
                </div>
            </div>
        </aside>

        <AppFooter />
    </div>
</template>
