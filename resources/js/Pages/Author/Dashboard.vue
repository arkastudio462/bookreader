<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Dashboard Penulis" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Penulis
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Total Karya</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ stats.total_books ?? 0 }}
                        </p>
                    </div>
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Terbit</p>
                        <p class="mt-2 text-3xl font-bold text-emerald-600">
                            {{ stats.published_books ?? 0 }}
                        </p>
                    </div>
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Menunggu Moderasi</p>
                        <p class="mt-2 text-3xl font-bold text-amber-600">
                            {{ stats.pending_books ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <Link :href="route('author.books.index')" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Kelola Buku Saya
                    </Link>
                    <Link :href="route('author.books.create')" class="ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Unggah Buku Baru
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
