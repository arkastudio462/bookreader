<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Dashboard Penerbit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">
                Dashboard Penerbit
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
                        <p class="lk text-sm text-muted">Terbit</p>
                        <p class="h mt-2 text-3xl text-ink">
                            {{ stats.published_books ?? 0 }}
                        </p>
                    </div>
                    <div class="card">
                        <p class="lk text-sm text-muted">Menunggu Moderasi</p>
                        <p class="h mt-2 text-3xl text-ink">
                            {{ stats.pending_books ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <Link :href="route('author.books.index')" class="btn-primary">
                        Kelola Buku
                    </Link>
                    <Link :href="route('author.books.create')" class="btn-outline ml-3">
                        Unggah Buku Baru
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
