<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CoverImage from '@/Components/CoverImage.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    books: Array,
});

const search = ref('');

const filteredBooks = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.books;
    return props.books.filter(
        (b) =>
            (b.title || '').toLowerCase().includes(q) ||
            (b.author_name || '').toLowerCase().includes(q),
    );
});
</script>

<template>
    <Head title="Pustaka Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Pustaka Saya</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Akun</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Pustaka</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Pustaka Saya</h1>
                    <p class="text-body-md text-muted">Koleksi judul yang sudah Anda beli, siap dibaca di perangkat mana pun.</p>
                </div>
                <div class="relative w-full lg:w-80">
                    <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[18px] text-muted">search</span>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari di pustaka…"
                        class="field w-full pl-10"
                        aria-label="Cari di pustaka"
                    />
                </div>
            </header>

            <div v-if="!books.length" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[28px] text-muted">library_books</span>
                </div>
                <h2 class="mt-space-md text-title-lg text-ink">Pustaka masih kosong</h2>
                <p class="mx-auto mt-space-xs max-w-md text-body-md text-muted">Beli judul pertama Anda dan buku akan langsung muncul di sini.</p>
                <Link :href="route('catalog.index')" class="btn-primary mt-space-lg gap-2 px-space-lg">
                    Cari Buku
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </Link>
            </div>

            <template v-else>
                <div class="flex items-center justify-between">
                    <span class="text-body-sm text-muted">
                        Menampilkan <span class="font-semibold text-ink">{{ filteredBooks.length }}</span> dari
                        {{ books.length }} judul
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-soft px-2.5 py-1 text-badge-label text-ink">
                        <span class="material-symbols-outlined text-[14px]">cloud_done</span>
                        Tersinkron
                    </span>
                </div>

                <div
                    v-if="filteredBooks.length"
                    class="grid grid-cols-2 gap-space-lg sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
                >
                    <article
                        v-for="book in filteredBooks"
                        :key="book.id"
                        class="group flex flex-col rounded-lg bg-canvas p-space-sm transition-colors hover:bg-soft"
                    >
                        <Link :href="route('catalog.show', book.slug)" class="relative block overflow-hidden rounded bg-soft">
                            <div class="aspect-[3/4]">
                                <img
                                    v-if="book.cover_url"
                                    :src="book.cover_url"
                                    :alt="book.title"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    loading="lazy"
                                />
                                <CoverImage v-else :book="book" />
                            </div>
                            <span class="absolute left-2 top-2 rounded-full bg-ink px-2 py-0.5 text-badge-label text-white">Dimiliki</span>
                            <span class="material-symbols-outlined absolute right-2 top-2 grid h-8 w-8 place-items-center rounded-full bg-canvas/80 text-[18px] text-ink backdrop-blur-sm transition-colors hover:text-accent">favorite</span>
                        </Link>

                        <div class="mt-space-sm flex flex-1 flex-col justify-between">
                            <div>
                                <Link :href="route('catalog.show', book.slug)" class="text-action line-clamp-1 text-ink hover:underline">
                                    {{ book.title }}
                                </Link>
                                <span class="block truncate text-body-sm text-muted">{{ book.author_name }}</span>
                                <div class="mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-ink" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-caption font-semibold text-ink">{{ book.average_rating }}</span>
                                </div>
                            </div>

                            <div class="mt-space-sm flex items-center gap-space-xs border-t border-hairline-soft pt-space-xs">
                                <Link :href="route('reader.show', book.id)" class="flex-1 rounded-full bg-ink px-space-sm py-1.5 text-center text-caption text-white transition-opacity hover:opacity-90">
                                    Baca
                                </Link>
                                <Link
                                    :href="route('catalog.show', book.slug)"
                                    aria-label="Detail buku"
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-field text-ink transition-colors hover:bg-hair"
                                >
                                    <span class="material-symbols-outlined text-[16px]">info</span>
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                        <span class="material-symbols-outlined text-[28px] text-muted">search_off</span>
                    </div>
                    <h2 class="mt-space-md text-title-lg text-ink">Tidak ditemukan</h2>
                    <p class="mt-space-xs text-body-md text-muted">Tidak ada judul di pustaka yang cocok dengan pencarian Anda.</p>
                    <button type="button" class="btn-outline mt-space-lg text-sm" @click="search = ''">Bersihkan Pencarian</button>
                </div>
            </template>
        </div>
    </AuthenticatedLayout>
</template>
