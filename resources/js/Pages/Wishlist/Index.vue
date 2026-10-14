<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BookPrice from '@/Components/BookPrice.vue';
import CoverImage from '@/Components/CoverImage.vue';

defineProps({ wishlists: Array });

function remove(bookId) {
    router.delete(route('wishlist.destroy', bookId), { preserveScroll: true });
}
</script>

<template>
    <Head title="Wishlist Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Wishlist Saya</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Akun</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Wishlist</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Wishlist Saya</h1>
                    <p class="text-body-md text-muted">Judul yang sedang Anda tunggu atau pantau harganya.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-soft px-3 py-1.5 text-badge-label text-ink lg:self-auto">
                    <span class="material-symbols-outlined text-[16px]">favorite</span>
                    {{ wishlists.length }} judul
                </span>
            </header>

            <div v-if="wishlists.length === 0" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[28px] text-muted">favorite_border</span>
                </div>
                <h2 class="mt-space-md text-title-lg text-ink">Wishlist masih kosong</h2>
                <p class="mx-auto mt-space-xs max-w-md text-body-md text-muted">Simpan judul yang ingin Anda beli nanti agar mudah ditemukan kembali.</p>
                <Link :href="route('catalog.index')" class="btn-primary mt-space-lg gap-2 px-space-lg">
                    Jelajahi Katalog
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </Link>
            </div>

            <div v-else class="grid grid-cols-2 gap-space-lg sm:grid-cols-3 lg:grid-cols-4">
                <article v-for="w in wishlists" :key="w.id" class="group flex flex-col rounded-lg bg-canvas p-space-sm transition-colors hover:bg-soft">
                    <Link :href="route('catalog.show', w.book.slug)" class="relative block overflow-hidden rounded bg-soft">
                        <div class="aspect-[3/4]">
                            <img
                                v-if="w.book.cover_url"
                                :src="w.book.cover_url"
                                :alt="w.book.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />
                            <CoverImage v-else :book="w.book" />
                        </div>
                        <span
                            v-if="w.book.on_promo"
                            class="absolute left-2 top-2 rounded-full bg-accent px-2 py-0.5 text-badge-label text-white"
                        >
                            {{ w.book.discount_source === 'flash' ? 'Flash Sale' : 'Diskon' }} {{ w.book.discount_percent }}%
                        </span>
                    </Link>

                    <div class="mt-space-sm flex flex-1 flex-col justify-between">
                        <div>
                            <Link :href="route('catalog.show', w.book.slug)" class="text-action line-clamp-1 text-ink transition-colors hover:text-accent">
                                {{ w.book.title }}
                            </Link>
                            <p class="truncate text-body-sm text-muted">{{ w.book.author_name }}</p>
                            <div class="mt-1">
                                <BookPrice :book="w.book" />
                            </div>
                        </div>

                        <div class="mt-space-sm flex items-center gap-space-xs border-t border-hairline-soft pt-space-xs">
                            <Link :href="route('catalog.show', w.book.slug)" class="flex-1 rounded-full bg-ink px-space-sm py-1.5 text-center text-caption text-white transition-opacity hover:opacity-90">
                                Lihat Detail
                            </Link>
                            <button
                                type="button"
                                aria-label="Hapus dari wishlist"
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-field text-muted transition-colors hover:bg-soft hover:text-error"
                                @click="remove(w.book_id)"
                            >
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
