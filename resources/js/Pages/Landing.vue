<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BookPrice from '@/Components/BookPrice.vue';
import FlashSaleCountdown from '@/Components/FlashSaleCountdown.vue';

defineProps({
    featuredBooks: Array,
    newestBooks: Array,
    flashSale: Object,
    popularCategories: Array,
    stats: Object,
});
</script>

<template>
    <Head title="PerpusKita - Platform Buku Digital" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">PerpusKita</h1>
                    <div class="flex items-center gap-4">
                        <Link :href="route('catalog.index')" class="text-gray-700 hover:text-gray-900">
                            Katalog
                        </Link>
                        <Link :href="route('login')" class="text-gray-700 hover:text-gray-900">
                            Masuk
                        </Link>
                        <Link :href="route('register')"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Daftar
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 py-20 text-white">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h2 class="text-4xl font-bold sm:text-5xl">
                    Jelajahi Ribuan Buku Digital
                </h2>
                <p class="mt-4 text-xl text-blue-100">
                    Baca, Tulis, dan Terbitkan Karya Anda Sendiri
                </p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link :href="route('catalog.index')"
                        class="rounded-lg bg-white px-6 py-3 font-semibold text-blue-600 hover:bg-gray-100">
                        Mulai Membaca
                    </Link>
                    <Link :href="route('register')"
                        class="rounded-lg border-2 border-white px-6 py-3 font-semibold text-white hover:bg-blue-700">
                        Jadi Penulis
                    </Link>
                </div>
            </div>
        </section>

        <!-- Flash Sale dengan hitung mundur (FR-4.1.2 / FR-4.7.9) -->
        <section v-if="flashSale && flashSale.items && flashSale.items.length"
            class="bg-gradient-to-r from-red-600 to-orange-500 py-12 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-3xl font-bold">⚡ {{ flashSale.name }}</h3>
                        <p v-if="flashSale.banner_text" class="mt-1 text-red-100">{{ flashSale.banner_text }}</p>
                    </div>
                    <div class="text-center">
                        <div class="text-xs uppercase tracking-wide text-red-100">Berakhir dalam</div>
                        <FlashSaleCountdown :ends-at="flashSale.ends_at" class="mt-1 justify-center" />
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-6">
                    <Link v-for="item in flashSale.items" :key="item.id"
                        :href="route('catalog.show', item.book.slug)" class="group">
                        <div class="aspect-[2/3] overflow-hidden rounded-lg bg-white/20">
                            <div class="flex h-full items-center justify-center text-white/60">Cover</div>
                        </div>
                        <h4 class="mt-2 text-sm font-medium group-hover:underline">{{ item.book.title }}</h4>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="font-bold">Rp {{ Number(item.book.effective_price).toLocaleString('id-ID') }}</span>
                            <span class="text-sm text-red-100 line-through">Rp {{ Number(item.book.price).toLocaleString('id-ID') }}</span>
                        </div>
                        <div v-if="item.stock_limit" class="mt-2">
                            <div class="h-1.5 w-full rounded bg-white/30">
                                <div class="h-1.5 rounded bg-white" :style="{ width: item.sold_percent + '%' }"></div>
                            </div>
                            <span class="text-xs text-red-100">Terjual {{ item.sold_percent }}%</span>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Platform Stats -->
        <section class="border-b border-gray-200 bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600">{{ stats.total_books }}+</div>
                        <div class="mt-2 text-gray-600">Buku Terbit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600">{{ stats.total_authors }}+</div>
                        <div class="mt-2 text-gray-600">Penulis & Penerbit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600">{{ stats.total_readers }}+</div>
                        <div class="mt-2 text-gray-600">Pembaca Aktif</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Books -->
        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900">Buku Unggulan</h3>
                <div class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-6">
                    <Link v-for="book in featuredBooks" :key="book.id"
                        :href="route('catalog.show', book.slug)"
                        class="group">
                        <div class="aspect-[2/3] overflow-hidden rounded-lg bg-gray-200">
                            <div class="flex h-full items-center justify-center text-gray-400">
                                Cover
                            </div>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900 group-hover:text-blue-600">
                            {{ book.title }}
                        </h4>
                        <p class="text-sm text-gray-600">{{ book.author_name }}</p>
                        <div class="mt-1">
                            <BookPrice :book="book" />
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Newest Books -->
        <section class="bg-gray-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900">Buku Terbaru</h3>
                <div class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-4 lg:grid-cols-4">
                    <Link v-for="book in newestBooks" :key="book.id"
                        :href="route('catalog.show', book.slug)"
                        class="group">
                        <div class="aspect-[2/3] overflow-hidden rounded-lg bg-gray-200">
                            <div class="flex h-full items-center justify-center text-gray-400">
                                Cover
                            </div>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900 group-hover:text-blue-600">
                            {{ book.title }}
                        </h4>
                        <p class="text-sm text-gray-600">{{ book.author_name }}</p>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-sm text-yellow-500">★ {{ book.average_rating }}</span>
                            <BookPrice :book="book" />
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Popular Categories -->
        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900">Kategori Populer</h3>
                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <Link v-for="cat in popularCategories" :key="cat.id"
                        :href="route('catalog.index', { category: cat.slug })"
                        class="rounded-lg border-2 border-gray-200 p-4 text-center hover:border-blue-600 hover:bg-blue-50">
                        <div class="font-semibold text-gray-900">{{ cat.name }}</div>
                        <div class="mt-1 text-sm text-gray-600">{{ cat.books_count }} buku</div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 py-12 text-gray-400">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <p>&copy; 2026 PerpusKita. Platform Buku Digital Multi-Role.</p>
            </div>
        </footer>
    </div>
</template>
