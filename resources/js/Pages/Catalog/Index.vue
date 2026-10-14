<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import BookCard from '@/Components/BookCard.vue';

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
</script>

<template>
    <Head title="Katalog buku" />

    <div class="flex min-h-screen flex-col bg-canvas">
        <AppHeader />

        <main class="mx-auto w-full max-w-6xl flex-1 px-5 pt-16">
            <header>
                <span v-if="onlyPromo" class="badge-accent">Sedang promo</span>
                <h1 class="h text-5xl md:text-[56px]">Katalog buku.</h1>
                <p class="lt mt-4 max-w-xl text-xl text-muted">
                    {{ books.total }} judul dari penulis dan penerbit mandiri Indonesia.
                </p>
            </header>

            <!-- Filter bar -->
            <div class="card-soft mt-10 flex flex-wrap items-center gap-3">
                <label class="sr-only" for="catalog-search">Cari buku</label>
                <input
                    id="catalog-search"
                    v-model="search"
                    type="search"
                    placeholder="Cari judul atau penulis…"
                    class="field h-12 w-full sm:w-72"
                />

                <label class="sr-only" for="catalog-category">Kategori</label>
                <select id="catalog-category" v-model="selectedCategory" class="field h-12">
                    <option value="">Semua kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>

                <label class="sr-only" for="catalog-sort">Urutkan</label>
                <select id="catalog-sort" v-model="selectedSort" class="field h-12">
                    <option value="newest">Terbaru</option>
                    <option value="bestseller">Terlaris</option>
                    <option value="cheapest">Termurah</option>
                    <option value="rating">Rating tertinggi</option>
                </select>

                <button
                    type="button"
                    class="lk h-12 rounded-full px-5 text-sm transition-colors"
                    :class="onlyPromo ? 'bg-ink text-white' : 'bg-canvas text-ink hover:bg-field'"
                    :aria-pressed="onlyPromo"
                    @click="onlyPromo = !onlyPromo"
                >
                    Flash sale
                </button>
            </div>

            <!-- Book grid -->
            <div v-if="books.data.length" class="mt-10 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                <BookCard v-for="book in books.data" :key="book.id" :book="book" />
            </div>
            <div v-else class="card mt-10 py-16 text-center">
                <p class="h text-2xl">Tidak ada buku yang cocok.</p>
                <p class="lt mt-2 text-muted">Coba ubah kata kunci atau kosongkan filter.</p>
            </div>

            <!-- Pagination -->
            <nav
                v-if="books.last_page > 1"
                class="mt-12 flex flex-wrap justify-center gap-2"
                aria-label="Navigasi halaman"
            >
                <Link
                    v-for="p in books.last_page"
                    :key="p"
                    :href="route('catalog.index', { ...query, page: p })"
                    class="lk grid h-10 w-10 place-items-center rounded-full text-sm transition-colors"
                    :class="p === books.current_page ? 'bg-ink text-white' : 'bg-soft text-muted hover:bg-field hover:text-ink'"
                >
                    {{ p }}
                </Link>
            </nav>
        </main>

        <AppFooter />
    </div>
</template>
