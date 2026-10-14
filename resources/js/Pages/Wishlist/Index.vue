<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BookPrice from '@/Components/BookPrice.vue';

defineProps({ wishlists: Array });

function remove(bookId) {
    router.delete(route('wishlist.destroy', bookId), { preserveScroll: true });
}
</script>

<template>
    <Head title="Wishlist Saya - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl">Wishlist Saya</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="wishlists.length === 0" class="card p-12 text-center">
                    <p class="text-muted">Wishlist kosong.</p>
                    <Link :href="route('catalog.index')" class="lk mt-4 inline-block text-ink hover:underline">
                        Jelajahi katalog
                    </Link>
                </div>

                <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    <div v-for="w in wishlists" :key="w.id" class="group">
                        <Link :href="route('catalog.show', w.book.slug)">
                            <div class="relative aspect-[2/3] rounded-sm bg-soft">
                                <span v-if="w.book.on_promo"
                                    class="badge-accent absolute left-2 top-2 px-2 py-0.5 text-xs">
                                    {{ w.book.discount_source === 'flash' ? '⚡ Flash Sale' : 'Diskon' }}
                                </span>
                            </div>
                            <h3 class="lk mt-2 text-ink group-hover:text-accent">{{ w.book.title }}</h3>
                            <p class="lt text-sm text-muted">{{ w.book.author_name }}</p>
                            <div class="mt-1">
                                <BookPrice :book="w.book" />
                            </div>
                        </Link>
                        <button @click="remove(w.book_id)" class="lk mt-2 text-sm text-muted hover:text-ink">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
