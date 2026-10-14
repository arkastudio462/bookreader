<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    items: Array,
    subtotal: Number,
    points_balance: Number,
});

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');

function remove(bookId) {
    router.delete(route('cart.destroy', bookId), { preserveScroll: true });
}
</script>

<template>
    <Head title="Keranjang - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Keranjang Belanja</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div v-if="!items.length" class="card p-12 text-center">
                    <p class="text-muted">Keranjang Anda masih kosong.</p>
                    <Link :href="route('catalog.index')" class="lk mt-4 inline-block text-ink hover:underline">
                        Jelajahi katalog
                    </Link>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="card flex items-center gap-4"
                    >
                        <div class="h-20 w-14 shrink-0 overflow-hidden rounded-sm bg-soft">
                            <img
                                v-if="item.book.cover_url"
                                :src="item.book.cover_url"
                                :alt="item.book.title"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <Link
                                :href="route('catalog.show', item.book.slug)"
                                class="lk truncate text-ink hover:underline"
                            >
                                {{ item.book.title }}
                            </Link>
                            <p class="truncate text-sm text-muted">{{ item.book.author_name }}</p>
                        </div>
                        <div class="lk text-ink">{{ rupiah(item.book.effective_price) }}</div>
                        <button @click="remove(item.book_id)" class="text-sm text-muted hover:text-ink hover:underline">
                            Hapus
                        </button>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between">
                            <span class="lk text-lg text-ink">Subtotal</span>
                            <span class="lk text-lg text-ink">{{ rupiah(subtotal) }}</span>
                        </div>
                        <p class="mt-1 text-sm text-muted">Poin tersedia: {{ points_balance }}</p>
                        <Link
                            :href="route('checkout.index')"
                            class="btn-primary mt-4 block text-center"
                        >
                            Lanjut ke Checkout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
