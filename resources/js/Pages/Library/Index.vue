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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Pustaka Saya</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="!books.length" class="rounded-lg bg-white p-12 text-center shadow">
                    <p class="text-gray-600">Belum ada buku di pustaka Anda.</p>
                    <Link :href="route('catalog.index')" class="mt-4 inline-block text-blue-600 hover:underline">
                        Cari buku
                    </Link>
                </div>

                <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-5">
                    <div v-for="book in books" :key="book.id" class="rounded-lg bg-white p-3 shadow">
                        <div class="aspect-[2/3] rounded bg-gray-200"></div>
                        <h3 class="mt-2 truncate text-sm font-medium text-gray-900">{{ book.title }}</h3>
                        <p class="truncate text-xs text-gray-600">{{ book.author_name }}</p>
                        <Link
                            :href="route('reader.show', book.id)"
                            class="mt-2 block rounded bg-green-600 py-2 text-center text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Baca
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
