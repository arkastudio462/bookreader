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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Keranjang Belanja</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div v-if="!items.length" class="rounded-lg bg-white p-12 text-center shadow">
                    <p class="text-gray-600">Keranjang Anda masih kosong.</p>
                    <Link :href="route('catalog.index')" class="mt-4 inline-block text-blue-600 hover:underline">
                        Jelajahi katalog
                    </Link>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="flex items-center gap-4 rounded-lg bg-white p-4 shadow"
                    >
                        <div class="h-20 w-14 shrink-0 rounded bg-gray-200"></div>
                        <div class="min-w-0 flex-1">
                            <Link
                                :href="route('catalog.show', item.book.slug)"
                                class="font-medium text-gray-900 hover:text-blue-600"
                            >
                                {{ item.book.title }}
                            </Link>
                            <p class="truncate text-sm text-gray-600">{{ item.book.author_name }}</p>
                        </div>
                        <div class="font-semibold text-blue-600">{{ rupiah(item.book.effective_price) }}</div>
                        <button @click="remove(item.book_id)" class="text-sm text-red-600 hover:underline">
                            Hapus
                        </button>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between text-lg font-semibold text-gray-900">
                            <span>Subtotal</span>
                            <span>{{ rupiah(subtotal) }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Poin tersedia: {{ points_balance }}</p>
                        <Link
                            :href="route('checkout.index')"
                            class="mt-4 block rounded-lg bg-blue-600 py-3 text-center font-semibold text-white hover:bg-blue-700"
                        >
                            Lanjut ke Checkout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
