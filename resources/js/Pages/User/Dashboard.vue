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
            <h2 class="h text-xl text-ink">
                Beranda Pembaca
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <div class="card">
                    <h3 class="h text-lg text-ink">
                        Selamat datang di PerpusKita
                    </h3>
                    <p class="lt mt-1 text-sm text-muted">
                        Lanjutkan membaca buku dari perpustakaan Anda atau jelajahi
                        <Link :href="route('catalog.index')" class="lk text-ink hover:text-ink-soft">katalog</Link>.
                    </p>
                </div>

                <div class="card">
                    <h3 class="h mb-4 text-lg text-ink">Perpustakaan Saya</h3>

                    <p v-if="!library.length" class="lt text-sm text-muted">
                        Belum ada buku. Buku gratis dan buku yang Anda beli akan muncul di sini.
                    </p>

                    <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                        <div v-for="b in library" :key="b.id" class="flex flex-col">
                            <div class="aspect-[2/3] rounded-md bg-hair"></div>
                            <h4 class="lk mt-2 line-clamp-2 text-sm text-ink">{{ b.title }}</h4>
                            <p class="text-xs text-muted">{{ b.author_name }}</p>

                            <div class="mt-2 h-1.5 w-full rounded-full bg-hair">
                                <div class="h-1.5 rounded-full bg-ink" :style="{ width: b.percent + '%' }"></div>
                            </div>
                            <p class="mt-1 text-xs text-muted">
                                <span v-if="b.percent > 0">{{ b.percent }}% selesai</span>
                                <span v-else>Belum dibaca</span>
                            </p>

                            <Link :href="route('reader.show', b.id)"
                                class="btn-primary mt-2 w-full">
                                {{ b.percent > 0 ? 'Lanjutkan' : 'Baca' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
