<script setup>
import { Head, Link } from '@inertiajs/vue3';
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
            <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                <Link :href="route('landing')" class="transition-colors hover:text-ink">Beranda</Link>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <Link :href="route('catalog.index')" class="transition-colors hover:text-ink">Katalog</Link>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="font-semibold text-ink">Penerbit</span>
            </nav>

            <section class="mt-space-md rounded-lg bg-soft p-space-md md:p-space-xl">
                <div class="flex flex-col gap-space-lg md:flex-row md:items-start md:justify-between">
                    <div class="space-y-space-sm">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-ink px-3 py-1 text-badge-label text-white">
                            <span class="material-symbols-outlined text-[14px]">corporate_fare</span>
                            Penerbit
                        </span>
                        <h1 class="text-headline-sm tracking-tight text-ink md:text-headline-lg">
                            {{ publisher.imprint_name || publisher.name }}
                        </h1>
                        <div class="flex flex-wrap gap-space-xs">
                            <span v-if="publisher.legal_name" class="rounded-full bg-canvas px-2.5 py-1 text-badge-label text-ink">
                                Badan hukum: {{ publisher.legal_name }}
                            </span>
                            <span class="rounded-full bg-canvas px-2.5 py-1 text-badge-label text-ink">Mitra Terverifikasi</span>
                        </div>
                        <p v-if="publisher.address" class="max-w-2xl text-body-md text-muted">{{ publisher.address }}</p>
                        <a
                            v-if="publisher.website"
                            :href="publisher.website"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-1 text-action text-ink transition-colors hover:text-accent"
                        >
                            {{ publisher.website }}
                            <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                        </a>
                    </div>

                    <div class="flex shrink-0 gap-space-sm">
                        <div class="rounded-2xl bg-canvas p-space-md text-center">
                            <div class="text-title-lg text-ink">{{ books.length }}</div>
                            <div class="text-caption text-muted">Terbitan</div>
                        </div>
                        <div class="rounded-2xl bg-canvas p-space-md text-center">
                            <div class="text-title-lg text-ink">30%</div>
                            <div class="text-caption text-muted">Imprint</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-margin-lg">
                <div class="flex items-end justify-between border-b border-hair pb-space-sm">
                    <h2 class="text-title-lg text-ink">Terbitan</h2>
                    <span class="text-body-sm text-muted">{{ books.length }} judul</span>
                </div>

                <div v-if="books.length" class="mt-space-lg grid grid-cols-2 gap-space-lg sm:grid-cols-3 lg:grid-cols-4">
                    <BookCard v-for="book in books" :key="book.id" :book="book" />
                </div>

                <div v-else class="mt-space-lg rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                        <span class="material-symbols-outlined text-[28px] text-muted">corporate_fare</span>
                    </div>
                    <p class="mt-space-md text-title-lg text-ink">Belum ada buku terbit</p>
                    <p class="mt-space-xs text-body-md text-muted">Katalog penerbit ini masih kosong.</p>
                </div>
            </section>
        </main>

        <AppFooter />
    </div>
</template>
