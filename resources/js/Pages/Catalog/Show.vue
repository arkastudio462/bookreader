<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
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

function addToWishlist() {
    router.post(route('wishlist.store'), { book_id: props.book.id }, {
        preserveScroll: true,
        onSuccess: () => {},
    });
}

function addToCart() {
    router.post(route('cart.store'), { book_id: props.book.id }, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="book.title + ' - PerpusKita'" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <Link :href="route('landing')" class="text-2xl font-bold text-gray-900">PerpusKita</Link>
                    <div class="flex items-center gap-4">
                        <Link :href="route('catalog.index')">Katalog</Link>
                        <template v-if="user">
                            <Link :href="route('dashboard')" class="hover:text-blue-600">{{ user.name }}</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')">Masuk</Link>
                            <Link :href="route('register')" class="rounded-lg bg-blue-600 px-4 py-2 text-white">Daftar</Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-600">
                <Link :href="route('catalog.index')" class="hover:text-blue-600">Katalog</Link>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ book.title }}</span>
            </nav>

            <div v-if="flashError" class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ flashError }}
            </div>
            <div v-if="flashSuccess" class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ flashSuccess }}
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Cover -->
                <div class="lg:col-span-1">
                    <div class="aspect-[2/3] overflow-hidden rounded-lg bg-gray-200">
                        <div class="flex h-full items-center justify-center text-gray-400">Cover</div>
                    </div>
                </div>

                <!-- Details -->
                <div class="lg:col-span-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ book.title }}</h1>
                    <p class="mt-2 text-lg text-gray-600">oleh {{ book.author_name }}</p>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex items-center gap-1 text-yellow-500">
                            <span>★</span>
                            <span class="font-semibold">{{ book.average_rating }}</span>
                            <span class="text-gray-600">({{ book.rating_count }} ulasan)</span>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg bg-white p-6 shadow">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="text-3xl font-bold text-blue-600">
                                <span v-if="Number(book.effective_price ?? book.price) > 0">
                                    Rp {{ Number(book.effective_price ?? book.price).toLocaleString('id-ID') }}
                                </span>
                                <span v-else>Gratis</span>
                            </div>
                            <span v-if="book.on_promo" class="text-lg text-gray-400 line-through">
                                Rp {{ Number(book.base_price ?? book.price).toLocaleString('id-ID') }}
                            </span>
                            <span v-if="book.on_promo"
                                class="rounded bg-red-100 px-2 py-1 text-sm font-semibold text-red-600">
                                {{ book.discount_source === 'flash' ? '⚡ Flash Sale' : 'Diskon' }} {{ book.discount_percent }}%
                            </span>
                        </div>

                        <!-- Hitung mundur flash sale (FR-4.7.9) -->
                        <div v-if="book.discount_source === 'flash' && book.flash_ends_at"
                            class="mt-3 flex flex-wrap items-center gap-3">
                            <span class="text-sm font-medium text-red-600">Berakhir dalam</span>
                            <FlashSaleCountdown :ends-at="book.flash_ends_at" />
                            <span v-if="book.flash_sold_percent !== null && book.flash_sold_percent !== undefined"
                                class="text-sm text-gray-600">Terjual {{ book.flash_sold_percent }}%</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="flex gap-4">
                                <Link v-if="accessMode === 'full'" :href="route('reader.show', book.id)"
                                    class="flex-1 rounded-lg bg-green-600 py-3 text-center font-semibold text-white hover:bg-green-700">
                                    Baca Buku
                                </Link>
                                <button v-else-if="user" @click="addToCart" class="flex-1 rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700">
                                    Tambah ke Keranjang
                                </button>
                                <Link v-else :href="route('login')" class="flex-1 rounded-lg bg-blue-600 py-3 text-center font-semibold text-white hover:bg-blue-700">
                                    Beli Sekarang
                                </Link>
                                <button @click="addToWishlist" class="rounded-lg border-2 border-gray-300 px-6 py-3 font-semibold text-gray-700 hover:bg-gray-50">
                                    + Wishlist
                                </button>
                            </div>
                            <Link v-if="accessMode === 'sample'" :href="route('reader.show', book.id)"
                                class="block rounded-lg border-2 border-blue-600 py-2 text-center font-semibold text-blue-600 hover:bg-blue-50">
                                Baca Sampel Gratis
                            </Link>
                            <Link v-else-if="!user" :href="route('login')" class="block text-center text-sm text-blue-600 hover:underline">
                                Masuk untuk membaca
                            </Link>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-gray-900">Sinopsis</h2>
                        <p class="mt-2 text-gray-700 leading-relaxed">{{ book.description }}</p>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Kategori:</span>
                            <Link v-if="book.category" :href="route('catalog.index', { category: book.category.slug })"
                                class="ml-2 text-blue-600 hover:underline">{{ book.category.name }}</Link>
                        </div>
                        <div>
                            <span class="text-gray-600">Bahasa:</span>
                            <span class="ml-2 text-gray-900">{{ book.language === 'id' ? 'Indonesia' : book.language }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Books -->
            <div v-if="relatedBooks.length" class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900">Buku Terkait</h2>
                <div class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-4">
                    <Link v-for="book in relatedBooks" :key="book.id" :href="route('catalog.show', book.slug)" class="group">
                        <div class="aspect-[2/3] rounded-lg bg-gray-200"></div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ book.title }}</h3>
                        <p class="text-sm text-gray-600">{{ book.author_name }}</p>
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
