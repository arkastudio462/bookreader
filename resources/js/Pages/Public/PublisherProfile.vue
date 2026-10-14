<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    publisher: Object,
    books: Array,
});
</script>

<template>
    <Head :title="(publisher.imprint_name || publisher.name) + ' - Penerbit'" />

    <div class="min-h-screen bg-gray-50">
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <Link :href="route('landing')" class="text-2xl font-bold text-gray-900">PerpusKita</Link>
                    <Link :href="route('catalog.index')" class="text-gray-700 hover:text-gray-900">Katalog</Link>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-8 shadow">
                <h1 class="text-3xl font-bold text-gray-900">{{ publisher.imprint_name || publisher.name }}</h1>
                <p class="mt-1 text-gray-600">Penerbit</p>
                <p v-if="publisher.legal_name" class="mt-2 text-sm text-gray-500">Badan hukum: {{ publisher.legal_name }}</p>
                <p v-if="publisher.address" class="mt-2 text-gray-700">{{ publisher.address }}</p>
                <a v-if="publisher.website" :href="publisher.website" target="_blank" class="mt-2 inline-block text-blue-600 hover:underline">{{ publisher.website }}</a>
            </div>

            <h2 class="mt-10 text-2xl font-bold text-gray-900">Terbitan ({{ books.length }})</h2>
            <div v-if="books.length" class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                <Link v-for="book in books" :key="book.id" :href="route('catalog.show', book.slug)" class="group">
                    <div class="aspect-[2/3] overflow-hidden rounded-lg bg-gray-200">
                        <img v-if="book.cover_path" :src="book.cover_path" :alt="book.title" class="h-full w-full object-cover">
                        <div v-else class="flex h-full items-center justify-center text-gray-400">Cover</div>
                    </div>
                    <h3 class="mt-2 font-medium text-gray-900 group-hover:text-blue-600">{{ book.title }}</h3>
                    <p class="text-sm text-gray-600">{{ book.author_name }}</p>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-sm text-yellow-500">★ {{ book.average_rating }}</span>
                        <span class="font-semibold text-blue-600">Rp {{ Number(book.price).toLocaleString('id-ID') }}</span>
                    </div>
                </Link>
            </div>
            <p v-else class="mt-6 text-gray-500">Belum ada buku terbit.</p>
        </main>
    </div>
</template>
