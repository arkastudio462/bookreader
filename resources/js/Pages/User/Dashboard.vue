<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    library: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Beranda Pembaca
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">
                        Selamat datang di PerpusKita
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Lanjutkan membaca buku dari perpustakaan Anda atau jelajahi
                        <Link :href="route('catalog.index')" class="text-blue-600 hover:underline">katalog</Link>.
                    </p>
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Perpustakaan Saya</h3>

                    <p v-if="!library.length" class="text-sm text-gray-500">
                        Belum ada buku. Buku gratis dan buku yang Anda beli akan muncul di sini.
                    </p>

                    <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                        <div v-for="b in library" :key="b.id" class="flex flex-col">
                            <div class="aspect-[2/3] rounded-lg bg-gray-200"></div>
                            <h4 class="mt-2 line-clamp-2 text-sm font-medium text-gray-900">{{ b.title }}</h4>
                            <p class="text-xs text-gray-500">{{ b.author_name }}</p>

                            <div class="mt-2 h-1.5 w-full rounded bg-gray-200">
                                <div class="h-1.5 rounded bg-blue-600" :style="{ width: b.percent + '%' }"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <span v-if="b.percent > 0">{{ b.percent }}% selesai</span>
                                <span v-else>Belum dibaca</span>
                            </p>

                            <Link :href="route('reader.show', b.id)"
                                class="mt-2 rounded-lg bg-green-600 py-2 text-center text-sm font-semibold text-white hover:bg-green-700">
                                {{ b.percent > 0 ? 'Lanjutkan' : 'Baca' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
