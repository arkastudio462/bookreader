<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import BookPrice from '@/Components/BookPrice.vue';

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

function applyFilters() {
    router.get(route('catalog.index'), {
        search: search.value,
        category: selectedCategory.value,
        sort: selectedSort.value,
        promo: onlyPromo.value ? 1 : undefined,
    }, { preserveState: true, replace: true });
}

watch([search, selectedCategory, selectedSort, onlyPromo], applyFilters);
</script>

<template>
    <Head title="Katalog Buku - PerpusKita" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <Link :href="route('landing')" class="text-2xl font-bold text-gray-900">PerpusKita</Link>
                    <div class="flex items-center gap-4">
                        <Link :href="route('catalog.index')" class="font-medium text-blue-600">Katalog</Link>
                        <Link :href="route('login')" class="text-gray-700 hover:text-gray-900">Masuk</Link>
                        <Link :href="route('register')" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Daftar</Link>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Katalog Buku</h1>
                <p class="text-gray-600">{{ books.total }} buku ditemukan</p>
            </div>

            <!-- Filters -->
            <div class="mt-6 flex flex-wrap items-center gap-4">
                <input v-model="search" type="text" placeholder="Cari judul, penulis..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 sm:w-64" />

                <select v-model="selectedCategory" class="rounded-lg border border-gray-300 px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>

                <select v-model="selectedSort" class="rounded-lg border border-gray-300 px-4 py-2">
                    <option value="newest">Terbaru</option>
                    <option value="bestseller">Terlaris</option>
                    <option value="cheapest">Termurah</option>
                    <option value="rating">Rating Tertinggi</option>
                </select>

                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 px-4 py-2">
                    <input v-model="onlyPromo" type="checkbox" class="rounded border-gray-300 text-blue-600" />
                    <span class="text-sm text-gray-700">Sedang Promo</span>
                </label>
            </div>

            <!-- Book Grid -->
            <div class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                <Link v-for="book in books.data" :key="book.id" :href="route('catalog.show', book.slug)" class="group">
                    <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-gray-200">
                        <div class="flex h-full items-center justify-center text-gray-400">Cover</div>
                        <span v-if="book.on_promo"
                            class="absolute left-2 top-2 rounded bg-red-600 px-2 py-0.5 text-xs font-semibold text-white">
                            {{ book.discount_source === 'flash' ? '⚡ Flash Sale' : 'Diskon' }}
                        </span>
                    </div>
                    <h3 class="mt-2 font-medium text-gray-900 group-hover:text-blue-600">{{ book.title }}</h3>
                    <p class="text-sm text-gray-600">{{ book.author_name }}</p>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-sm text-yellow-500">★ {{ book.average_rating }}</span>
                    </div>
                    <div class="mt-1">
                        <BookPrice :book="book" />
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="books.last_page > 1" class="mt-8 flex justify-center gap-2">
                <Link v-for="p in books.last_page" :key="p" :href="route('catalog.index', { page: p })"
                    :class="['px-3 py-1 rounded', p === books.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100']">
                    {{ p }}
                </Link>
            </div>
        </main>
    </div>
</template>
