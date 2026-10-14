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
            <h2 class="h text-xl text-ink">
                Dashboard Super Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="card">
                        <p class="lk text-sm text-muted">Total Buku</p>
                        <p class="h mt-2 text-3xl text-ink">
                            {{ stats.total_books ?? 0 }}
                        </p>
                    </div>
                    <div class="card">
                        <p class="lk text-sm text-muted">Menunggu Moderasi</p>
                        <p class="h mt-2 text-3xl text-ink">
                            {{ stats.pending_books ?? 0 }}
                        </p>
                    </div>
                    <div class="card">
                        <p class="lk text-sm text-muted">Total Pengguna</p>
                        <p class="h mt-2 text-3xl text-ink">
                            {{ stats.total_users ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 card">
                    <h3 class="h text-lg text-ink">Fondasi (Fase 1) aktif</h3>
                    <p class="lt mt-1 text-sm text-muted">
                        Autentikasi 4 role, penyimpanan R2, dan struktur dasar siap digunakan.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
