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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Wishlist Saya</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="wishlists.length === 0" class="rounded-lg bg-white p-12 text-center shadow">
                    <p class="text-gray-600">Wishlist kosong.</p>
                    <Link :href="route('catalog.index')" class="mt-4 inline-block text-blue-600 hover:underline">
                        Jelajahi katalog
                    </Link>
                </div>

                <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    <div v-for="w in wishlists" :key="w.id" class="group">
                        <Link :href="route('catalog.show', w.book.slug)">
                            <div class="relative aspect-[2/3] rounded-lg bg-gray-200">
                                <span v-if="w.book.on_promo"
                                    class="absolute left-2 top-2 rounded bg-red-600 px-2 py-0.5 text-xs font-semibold text-white">
                                    {{ w.book.discount_source === 'flash' ? '⚡ Flash Sale' : 'Diskon' }}
                                </span>
                            </div>
                            <h3 class="mt-2 font-medium text-gray-900 group-hover:text-blue-600">{{ w.book.title }}</h3>
                            <p class="text-sm text-gray-600">{{ w.book.author_name }}</p>
                            <div class="mt-1">
                                <BookPrice :book="w.book" />
                            </div>
                        </Link>
                        <button @click="remove(w.book_id)" class="mt-2 text-sm text-red-600 hover:underline">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
