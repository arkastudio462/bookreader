<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Super Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Total Buku</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ stats.total_books ?? 0 }}
                        </p>
                    </div>
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Menunggu Moderasi</p>
                        <p class="mt-2 text-3xl font-bold text-amber-600">
                            {{ stats.pending_books ?? 0 }}
                        </p>
                    </div>
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">Total Pengguna</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ stats.total_users ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">Fondasi (Fase 1) aktif</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Autentikasi 4 role, penyimpanan R2, dan struktur dasar siap digunakan.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
