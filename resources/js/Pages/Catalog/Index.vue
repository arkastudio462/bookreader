<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import CoverImage from '@/Components/CoverImage.vue';

const props = defineProps({
    books: Object,
    categories: Array,
    genres: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedSort = ref(props.filters.sort || 'newest');
const onlyPromo = ref(props.filters.promo === true || props.filters.promo === '1' || props.filters.promo === 1);
const layoutMode = ref('grid');
const maxPrice = ref(200000);
const minRating = ref(0);
const selectedGenres = ref([]);
const selectedPublishers = ref([]);

const query = computed(() => ({
    search: search.value || undefined,
    category: selectedCategory.value || undefined,
    sort: selectedSort.value !== 'newest' ? selectedSort.value : undefined,
    promo: onlyPromo.value ? 1 : undefined,
}));

function applyFilters() {
    router.get(route('catalog.index'), query.value, { preserveState: true, replace: true });
}

watch([search, selectedCategory, selectedSort, onlyPromo], applyFilters);

function discountPercent(book) {
    if (!book.original_price || !book.price) return 0;
    return Math.round(((book.original_price - book.price) / book.original_price) * 100);
}

const publisherNames = ['Matapena Press', 'Gramedia', 'Bentang', 'Mojok', 'Noura', 'Kompas Buku'];
</script>

<template>
    <Head title="Katalog Buku" />

    <div class="flex min-h-screen flex-col bg-canvas">
        <AppHeader />

        <main class="mx-auto w-full max-w-[1440px] flex-1 px-margin-lg pb-space-xl">
            <div class="mb-space-md flex items-center gap-space-xs pt-space-md text-caption text-muted">
                <Link :href="route('landing')" class="transition-colors hover:text-ink">Beranda</Link>
                <span>/</span>
                <span class="font-semibold text-ink">Katalog Buku</span>
            </div>

            <div class="mb-space-xl flex flex-col justify-between gap-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl">
                    <span class="mb-space-sm inline-block rounded-full bg-soft px-space-md py-space-xs text-badge-label tracking-wider text-ink uppercase">Arsip Literasi Digital</span>
                    <h1 class="mb-space-sm text-headline-md tracking-tight text-ink">
                        Jelajahi Ribuan Koleksi Buku Digital.
                    </h1>
                    <p class="max-w-2xl text-body-md leading-relaxed text-muted">
                        Koleksi terkurasi dari sastra klasik Nusantara, fiksi urban, pengembangan diri, bisnis, hingga sains dan teknologi format EPUB & PDF interaktif beresolusi tinggi.
                    </p>
                </div>
                <div class="flex items-center gap-space-lg rounded-lg bg-soft p-space-md">
                    <div class="flex flex-col">
                        <span class="text-title-lg font-bold tracking-tight text-ink">{{ books.total }}</span>
                        <span class="text-caption text-muted">Total Judul</span>
                    </div>
                    <div class="h-8 w-px bg-hair"></div>
                    <div class="flex flex-col">
                        <span class="text-title-lg font-bold tracking-tight text-ink">142</span>
                        <span class="text-caption text-muted">Penerbit Resmi</span>
                    </div>
                    <div class="h-8 w-px bg-hair"></div>
                    <div class="flex flex-col">
                        <span class="text-title-lg font-bold tracking-tight text-ink">EPUB 3</span>
                        <span class="text-caption text-muted">Standar Bebas DRM</span>
                    </div>
                </div>
            </div>

            <div class="mb-space-xl flex flex-col gap-space-md">
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-space-md flex items-center text-muted">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari judul buku, nama penulis, topik, atau ISBN..."
                        class="h-14 w-full rounded-full bg-field pr-32 pl-12 text-body-md text-ink placeholder:text-faint transition-all focus:outline-none focus:ring-2 focus:ring-ink"
                    />
                    <div class="absolute inset-y-0 right-space-xs flex items-center gap-space-xs pr-space-xs">
                        <button type="button" class="flex h-10 items-center justify-center rounded-full bg-ink px-space-lg text-action text-white transition-opacity hover:opacity-90" @click="applyFilters">
                            Cari
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-space-xs overflow-x-auto pb-space-xs">
                    <button
                        type="button"
                        class="shrink-0 rounded-full px-space-md py-space-xs text-action text-body-sm transition-colors"
                        :class="!selectedCategory && !onlyPromo ? 'bg-ink text-white' : 'bg-soft text-ink hover:bg-hair'"
                        @click="selectedCategory = ''; onlyPromo = false"
                    >
                        Semua Koleksi
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        type="button"
                        class="shrink-0 rounded-full px-space-md py-space-xs text-action text-body-sm transition-colors"
                        :class="selectedCategory === cat.slug ? 'bg-ink text-white' : 'bg-soft text-ink hover:bg-hair'"
                        @click="selectedCategory = selectedCategory === cat.slug ? '' : cat.slug"
                    >
                        {{ cat.name }}
                    </button>
                    <button
                        type="button"
                        class="flex shrink-0 items-center gap-1 rounded-full px-space-md py-space-xs text-action text-body-sm transition-colors"
                        :class="onlyPromo ? 'bg-accent text-white' : 'bg-soft text-ink hover:bg-hair'"
                        @click="onlyPromo = !onlyPromo"
                    >
                        <span class="h-2 w-2 rounded-full bg-accent" :class="{ '!bg-white': onlyPromo }"></span>
                        Buku Gratis
                    </button>
                    <button
                        type="button"
                        class="flex shrink-0 items-center gap-1 rounded-full bg-accent px-space-md py-space-xs text-action text-body-sm text-white transition-colors"
                        @click="onlyPromo = true"
                    >
                        <span class="material-symbols-outlined text-[16px]">bolt</span>
                        Flash Sale
                    </button>
                </div>
            </div>

            <div class="mb-space-lg flex flex-col justify-between gap-space-md rounded-lg bg-soft px-space-md py-space-md md:flex-row md:items-center">
                <div class="flex flex-wrap items-center gap-space-sm">
                    <span class="text-body-sm font-semibold text-ink">{{ books.total }} judul ditemukan</span>
                    <div class="h-4 w-px bg-hair"></div>
                    <div class="flex items-center gap-space-xs">
                        <span v-if="selectedCategory" class="inline-flex items-center gap-1 rounded-full bg-canvas px-space-sm py-0.5 text-caption text-ink shadow-sm">
                            Kategori: {{ categories.find(c => c.slug === selectedCategory)?.name }}
                            <button class="text-muted transition-colors hover:text-ink" @click="selectedCategory = ''">&times;</button>
                        </span>
                        <span v-if="onlyPromo" class="inline-flex items-center gap-1 rounded-full bg-canvas px-space-sm py-0.5 text-caption text-ink shadow-sm">
                            Promo
                            <button class="text-muted transition-colors hover:text-ink" @click="onlyPromo = false">&times;</button>
                        </span>
                        <button class="ml-space-xs text-caption text-muted underline transition-colors hover:text-ink" @click="selectedCategory = ''; onlyPromo = false; search = ''">
                            Reset Semua
                        </button>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-space-md">
                    <div class="flex items-center rounded-full bg-canvas p-0.5">
                        <button
                            type="button"
                            title="Grid View"
                            class="flex items-center justify-center rounded-full p-1.5 transition-colors"
                            :class="layoutMode === 'grid' ? 'bg-ink text-white' : 'text-muted hover:text-ink'"
                            @click="layoutMode = 'grid'"
                        >
                            <span class="material-symbols-outlined text-[18px]">grid_view</span>
                        </button>
                        <button
                            type="button"
                            title="List View"
                            class="flex items-center justify-center rounded-full p-1.5 transition-colors"
                            :class="layoutMode === 'list' ? 'bg-ink text-white' : 'text-muted hover:text-ink'"
                            @click="layoutMode = 'list'"
                        >
                            <span class="material-symbols-outlined text-[18px]">format_list_bulleted</span>
                        </button>
                    </div>
                    <div class="relative">
                        <select v-model="selectedSort" class="flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-space-xs text-action text-body-sm text-ink shadow-sm transition-colors hover:bg-hairline-soft">
                            <option value="newest">Terbaru</option>
                            <option value="bestseller">Terpopuler</option>
                            <option value="cheapest">Termurah</option>
                            <option value="rating">Rating Tertinggi</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-start gap-space-xl lg:flex-row">
                <aside class="flex w-full shrink-0 flex-col gap-space-lg rounded-lg bg-soft p-space-lg lg:w-[280px]">
                    <div class="flex items-center justify-between border-b border-hair pb-space-sm">
                        <span class="flex items-center gap-space-xs text-title-md text-ink">
                            <span class="material-symbols-outlined text-[20px]">tune</span>
                            Filter Koleksi
                        </span>
                        <button class="text-caption text-muted transition-colors hover:text-ink" @click="selectedGenres = []; selectedPublishers = []">Hapus</button>
                    </div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Kategori & Genre</span>
                        <div class="flex flex-col gap-space-xs">
                            <label v-for="genre in genres.slice(0, 7)" :key="genre.id" class="flex cursor-pointer items-center justify-between text-body-sm text-ink">
                                <div class="flex items-center gap-space-sm">
                                    <input type="checkbox" :value="genre.id" v-model="selectedGenres" class="h-4 w-4 cursor-pointer rounded accent-ink" />
                                    <span>{{ genre.name }}</span>
                                </div>
                                <span class="text-caption text-muted">{{ genre.books_count || 0 }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="h-px w-full bg-hair"></div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Format Pembacaan</span>
                        <div class="flex flex-col gap-space-xs">
                            <label class="flex cursor-pointer items-center justify-between text-body-sm text-ink">
                                <div class="flex items-center gap-space-sm">
                                    <input type="checkbox" checked disabled class="h-4 w-4 cursor-pointer rounded accent-ink" />
                                    <span>EPUB 3 Responsif</span>
                                </div>
                                <span class="text-caption text-muted">1.120</span>
                            </label>
                            <label class="flex cursor-pointer items-center justify-between text-body-sm text-ink">
                                <div class="flex items-center gap-space-sm">
                                    <input type="checkbox" class="h-4 w-4 cursor-pointer rounded accent-ink" />
                                    <span>PDF Interaktif</span>
                                </div>
                                <span class="text-caption text-muted">308</span>
                            </label>
                        </div>
                    </div>

                    <div class="h-px w-full bg-hair"></div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Rentang Harga</span>
                        <div class="grid grid-cols-2 gap-space-xs">
                            <button type="button" class="rounded-full bg-canvas px-space-sm py-1.5 text-caption text-center text-ink transition-colors hover:bg-hair">Gratis (Rp0)</button>
                            <button type="button" class="rounded-full bg-canvas px-space-sm py-1.5 text-caption text-center text-ink transition-colors hover:bg-hair">&lt; Rp50.000</button>
                            <button type="button" class="rounded-full bg-ink px-space-sm py-1.5 text-caption text-center text-white transition-colors">Rp50k - Rp100k</button>
                            <button type="button" class="rounded-full bg-canvas px-space-sm py-1.5 text-caption text-center text-ink transition-colors hover:bg-hair">&gt; Rp100.000</button>
                        </div>
                        <div class="pt-space-xs">
                            <input type="range" min="0" max="200000" v-model="maxPrice" class="w-full accent-ink" />
                            <div class="mt-1 flex justify-between text-caption text-muted">
                                <span>Rp0</span>
                                <span>Maks: Rp{{ Number(maxPrice).toLocaleString('id-ID') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="h-px w-full bg-hair"></div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Rating Pembaca</span>
                        <div class="flex flex-col gap-space-xs">
                            <label class="flex cursor-pointer items-center gap-space-sm text-body-sm text-ink">
                                <input type="radio" :value="4.5" v-model="minRating" class="cursor-pointer accent-ink" name="rating" />
                                <div class="flex items-center text-ink">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]">star_half</span>
                                    <span class="ml-1 text-caption text-muted">4.5 ke atas</span>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-space-sm text-body-sm text-ink">
                                <input type="radio" :value="4" v-model="minRating" class="cursor-pointer accent-ink" name="rating" />
                                <div class="flex items-center text-ink">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]">star</span>
                                    <span class="ml-1 text-caption text-muted">4.0 ke atas</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="h-px w-full bg-hair"></div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Aksesibilitas</span>
                        <div class="flex flex-col gap-space-xs">
                            <label class="flex cursor-pointer items-center gap-space-sm text-body-sm text-ink">
                                <input type="checkbox" checked class="h-4 w-4 rounded accent-ink" />
                                <span>Sampel Gratis (Bab 1-3)</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-space-sm text-body-sm text-ink">
                                <input type="checkbox" class="h-4 w-4 rounded accent-ink" />
                                <span>Bisa Dibaca Offline</span>
                            </label>
                        </div>
                    </div>

                    <div class="h-px w-full bg-hair"></div>

                    <div class="flex flex-col gap-space-sm">
                        <span class="text-action text-body-sm tracking-wide text-ink uppercase">Penerbit Resmi</span>
                        <div class="flex flex-wrap gap-space-xs">
                            <button
                                v-for="pub in publisherNames"
                                :key="pub"
                                type="button"
                                class="rounded-full px-space-sm py-1 text-caption transition-colors"
                                :class="selectedPublishers.includes(pub) ? 'bg-ink text-white' : 'bg-canvas text-ink hover:bg-hair'"
                                @click="selectedPublishers.includes(pub) ? selectedPublishers = selectedPublishers.filter(p => p !== pub) : selectedPublishers.push(pub)"
                            >
                                {{ pub }}
                            </button>
                        </div>
                    </div>
                </aside>

                <div class="flex w-full flex-1 flex-col gap-space-xl">
                    <div class="flex w-full flex-col items-center justify-between gap-space-md rounded-lg bg-field p-space-lg md:flex-row">
                        <div class="flex items-center gap-space-md">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-ink text-white">
                                <span class="material-symbols-outlined text-[24px]">history_edu</span>
                            </div>
                            <div>
                                <span class="text-accent text-badge-label uppercase tracking-wider block">Pilihan Kurator Pekan Ini</span>
                                <h2 class="text-title-md tracking-tight text-ink">Menyelami Arus Sejarah & Identitas Nusantara</h2>
                                <p class="text-body-sm text-muted">Koleksi 12 naskah arsip terpilih dan interpretasi historiografis modern.</p>
                            </div>
                        </div>
                        <button type="button" class="shrink-0 rounded-full bg-ink px-space-lg py-space-xs text-action text-white transition-opacity hover:opacity-90">
                            Buka Kurasi
                        </button>
                    </div>

                    <div v-if="books.data.length" :class="layoutMode === 'grid' ? 'grid grid-cols-1 gap-space-lg sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4' : 'flex flex-col gap-space-lg'">
                        <div v-for="book in books.data" :key="book.id" class="group flex flex-col rounded-lg bg-canvas p-space-sm transition-colors hover:bg-soft" :class="{ '!flex-row !p-4': layoutMode === 'list' }">
                            <Link :href="route('catalog.show', book.slug)" class="relative mb-space-sm block w-full overflow-hidden rounded bg-hairline-soft" :class="{ '!mb-0 !w-32 !shrink-0': layoutMode === 'list' }">
                                <div class="aspect-[3/4]" :class="{ '!aspect-auto !h-full': layoutMode === 'list' }">
                                    <img
                                        v-if="book.cover_url"
                                        :src="book.cover_url"
                                        :alt="book.title"
                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    />
                                    <CoverImage v-else :book="book" />
                                </div>
                                <span v-if="discountPercent(book) > 0" class="absolute top-2 left-2 rounded-full bg-accent px-space-sm py-0.5 text-badge-label text-white">
                                    -{{ discountPercent(book) }}%
                                </span>
                                <button
                                    type="button"
                                    aria-label="Wishlist"
                                    class="absolute top-2 right-2 flex h-8 w-8 items-center justify-center rounded-full bg-canvas/80 text-ink shadow-sm backdrop-blur-sm transition-colors hover:text-accent"
                                >
                                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                                </button>
                                <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between rounded-full bg-scrim px-2 py-1 text-caption text-white backdrop-blur-sm">
                                    <span class="truncate">{{ book.category?.name || 'Fiksi' }}</span>
                                    <span>EPUB 3</span>
                                </div>
                            </Link>
                            <div class="flex flex-1 flex-col justify-between" :class="{ '!ml-4': layoutMode === 'list' }">
                                <div>
                                    <Link :href="route('catalog.show', book.slug)" class="text-action text-ink line-clamp-1 hover:underline">
                                        {{ book.title }}
                                    </Link>
                                    <span class="text-body-sm text-muted block truncate">{{ book.author_name }}</span>
                                    <div class="mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-ink text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="text-caption font-semibold text-ink">{{ book.average_rating }}</span>
                                        <span class="text-caption text-muted">({{ book.ratings_count || 0 }})</span>
                                    </div>
                                </div>
                                <div class="mt-space-sm flex items-center justify-between border-t border-hairline-soft pt-space-xs">
                                    <div>
                                        <span class="text-action text-body-md text-ink block">Rp{{ Number(book.price).toLocaleString('id-ID') }}</span>
                                        <span v-if="book.original_price && book.original_price > book.price" class="text-caption text-faint line-through">Rp{{ Number(book.original_price).toLocaleString('id-ID') }}</span>
                                    </div>
                                    <Link :href="route('catalog.show', book.slug)" class="rounded-full bg-ink px-space-md py-1 text-caption text-white transition-opacity hover:opacity-90">
                                        Beli
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-16 text-center">
                        <p class="text-title-lg text-ink">Tidak ada buku yang cocok.</p>
                        <p class="mt-2 text-muted">Coba ubah kata kunci atau kosongkan filter.</p>
                    </div>

                    <nav v-if="books.last_page > 1" class="mt-4 flex flex-wrap justify-center gap-2" aria-label="Navigasi halaman">
                        <Link
                            v-for="p in books.last_page"
                            :key="p"
                            :href="route('catalog.index', { ...query, page: p })"
                            class="grid h-10 w-10 place-items-center rounded-full text-sm transition-colors"
                            :class="p === books.current_page ? 'bg-ink text-white' : 'bg-soft text-muted hover:bg-field hover:text-ink'"
                        >
                            {{ p }}
                        </Link>
                    </nav>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>
