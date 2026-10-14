<script setup>
import { Link } from '@inertiajs/vue3';
import CoverImage from '@/Components/CoverImage.vue';

defineProps({
    book: { type: Object, required: true },
    showSold: { type: Boolean, default: false },
});
</script>

<template>
    <Link :href="route('catalog.show', book.slug)" class="group flex flex-col rounded-lg bg-canvas p-space-sm transition-colors hover:bg-soft">
        <div class="relative block overflow-hidden rounded bg-soft">
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
            <span v-if="book.discount_percent" class="absolute left-2 top-2 rounded-full bg-accent px-2 py-0.5 text-badge-label text-white">
                Diskon {{ book.discount_percent }}%
            </span>
            <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between rounded-full bg-scrim px-2 py-1 text-caption text-white backdrop-blur-sm">
                <span class="truncate">{{ book.category?.name || 'Umum' }}</span>
                <span>EPUB 3</span>
            </div>
        </div>

        <div class="mt-space-sm flex flex-1 flex-col justify-between">
            <div>
                <h3 class="text-action line-clamp-1 text-ink group-hover:underline">{{ book.title }}</h3>
                <p class="truncate text-body-sm text-muted">{{ book.author_name }}</p>
                <p v-if="book.average_rating" class="mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-ink" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="text-caption font-semibold text-ink">{{ book.average_rating }}</span>
                </p>
            </div>

            <div class="mt-space-sm flex items-end justify-between gap-space-sm border-t border-hairline-soft pt-space-xs">
                <div class="min-w-0">
                    <span class="block text-action text-ink">
                        {{ Number(book.effective_price ?? book.price) > 0 ? 'Rp ' + Number(book.effective_price ?? book.price).toLocaleString('id-ID') : 'Gratis' }}
                    </span>
                    <s v-if="book.on_promo" class="block text-caption text-faint">
                        Rp {{ Number(book.base_price ?? book.price).toLocaleString('id-ID') }}
                    </s>
                </div>
            </div>

            <div v-if="showSold && book.sold_percent" class="mt-space-sm">
                <div class="h-1.5 overflow-hidden rounded-full bg-field">
                    <div class="h-full rounded-full bg-ink" :style="{ width: book.sold_percent + '%' }"></div>
                </div>
                <p class="mt-1 text-caption text-muted">Terjual {{ book.sold_percent }}%</p>
            </div>
        </div>
    </Link>
</template>
