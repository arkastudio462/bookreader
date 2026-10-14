<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    books: Array,
});
</script>

<template>
    <Head title="Pustaka Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl">Pustaka Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="!books.length" class="card p-12 text-center">
                    <p class="text-muted">Belum ada buku di pustaka Anda.</p>
                    <Link :href="route('catalog.index')" class="lk mt-4 inline-block text-ink hover:underline">
                        Cari buku
                    </Link>
                </div>

                <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-5">
                    <div v-for="book in books" :key="book.id" class="card p-3">
                        <div class="aspect-[2/3] rounded-sm bg-soft"></div>
                        <h3 class="lk mt-2 truncate text-sm text-ink">{{ book.title }}</h3>
                        <p class="lt truncate text-xs text-muted">{{ book.author_name }}</p>
                        <Link
                            :href="route('reader.show', book.id)"
                            class="btn-primary mt-2 block py-2 text-center text-sm"
                        >
                            Baca
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
