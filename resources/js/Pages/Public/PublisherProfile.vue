<script setup>
import { Head } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import BookCard from '@/Components/BookCard.vue';

defineProps({
    publisher: Object,
    books: Array,
});
</script>

<template>
    <Head :title="(publisher.imprint_name || publisher.name) + ' — penerbit'" />

    <div class="flex min-h-screen flex-col bg-canvas">
        <AppHeader />

        <main class="mx-auto w-full max-w-6xl flex-1 px-5 pt-16">
            <div class="card-soft">
                <span class="badge-overlay">Penerbit</span>
                <h1 class="h mt-4 text-4xl md:text-[56px]">{{ publisher.imprint_name || publisher.name }}</h1>
                <p v-if="publisher.legal_name" class="mt-4 text-sm text-muted">
                    Badan hukum: {{ publisher.legal_name }}
                </p>
                <p v-if="publisher.address" class="lt mt-2 max-w-2xl text-muted">{{ publisher.address }}</p>
                <a
                    v-if="publisher.website"
                    :href="publisher.website"
                    target="_blank"
                    rel="noopener"
                    class="lk mt-4 inline-block text-ink hover:underline"
                >
                    {{ publisher.website }}
                </a>
            </div>

            <h2 class="h mt-24 text-3xl">Terbitan ({{ books.length }}).</h2>
            <div v-if="books.length" class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                <BookCard v-for="book in books" :key="book.id" :book="book" />
            </div>
            <p v-else class="lt mt-6 text-muted">Belum ada buku terbit.</p>
        </main>

        <AppFooter />
    </div>
</template>
