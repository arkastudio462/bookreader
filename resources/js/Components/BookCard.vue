<script setup>
defineProps({
    book: { type: Object, required: true },
    showSold: { type: Boolean, default: false },
});
</script>

<template>
    <Link :href="route('catalog.show', book.slug)" class="group block">
        <div class="relative">
            <div class="aspect-[3/4] overflow-hidden rounded-sm bg-soft">
                <img
                    v-if="book.cover_url"
                    :src="book.cover_url"
                    :alt="book.title"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                    loading="lazy"
                />
                <div v-else class="flex h-full items-center justify-center text-faint">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20V4H6.5A2.5 2.5 0 004 6.5v13z" />
                        <path d="M4 19.5A2.5 2.5 0 006.5 22H20v-5" />
                    </svg>
                </div>
            </div>
            <div v-if="book.discount_percent" class="absolute left-2 top-2">
                <span class="badge-overlay">Diskon {{ book.discount_percent }}%</span>
            </div>
        </div>
        <h3 class="lk mt-3 truncate text-ink group-hover:underline">{{ book.title }}</h3>
        <p class="truncate text-sm text-muted">{{ book.author_name }}<template v-if="book.average_rating"> · ★ {{ book.average_rating }}</template></p>
        <p class="mt-1">
            <span class="lk text-ink">
                {{ Number(book.effective_price ?? book.price) > 0 ? 'Rp ' + Number(book.effective_price ?? book.price).toLocaleString('id-ID') : 'Gratis' }}
            </span>
            <s v-if="book.on_promo" class="ml-1 text-sm text-faint">Rp {{ Number(book.base_price ?? book.price).toLocaleString('id-ID') }}</s>
        </p>
        <div v-if="showSold && book.sold_percent" class="mt-2">
            <div class="h-1.5 overflow-hidden rounded-full bg-soft">
                <div class="h-full rounded-full bg-ink" :style="{ width: book.sold_percent + '%' }"></div>
            </div>
            <p class="mt-1 text-xs text-muted">Terjual {{ book.sold_percent }}%</p>
        </div>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default { components: { Link } };
</script>
