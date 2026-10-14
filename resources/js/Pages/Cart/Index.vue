<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CoverImage from '@/Components/CoverImage.vue';

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

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Keranjang</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">{{ items.length }} Judul Dipilih</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Keranjang Belanja</h1>
                    <p class="text-body-md text-muted">Tinjau judul yang akan Anda beli sebelum melanjutkan ke pembayaran.</p>
                </div>
                <Link :href="route('catalog.index')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Lanjut Belanja
                </Link>
            </header>

            <div v-if="!items.length" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[28px] text-muted">shopping_cart</span>
                </div>
                <h2 class="mt-space-md text-title-lg text-ink">Keranjang masih kosong</h2>
                <p class="mx-auto mt-space-xs max-w-md text-body-md text-muted">Telusuri katalog PerpusKita dan temukan judul yang ingin Anda miliki.</p>
                <Link :href="route('catalog.index')" class="btn-primary mt-space-lg gap-2 px-space-lg">
                    Jelajahi Katalog
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <section class="space-y-space-sm lg:col-span-7">
                    <div class="flex items-center justify-between">
                        <h2 class="text-title-md text-ink">Item di Keranjang</h2>
                        <span class="rounded-full bg-field px-2.5 py-1 text-badge-label text-ink">{{ items.length }} buku</span>
                    </div>

                    <article
                        v-for="item in items"
                        :key="item.id"
                        class="flex items-start gap-space-md rounded-[24px] border border-hair bg-canvas p-space-md transition-colors hover:border-ink"
                    >
                        <Link :href="route('catalog.show', item.book.slug)" class="w-20 shrink-0 overflow-hidden rounded-sm bg-soft">
                            <div class="aspect-[3/4]">
                                <img
                                    v-if="item.book.cover_url"
                                    :src="item.book.cover_url"
                                    :alt="item.book.title"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                                <CoverImage v-else :book="item.book" />
                            </div>
                        </Link>
                        <div class="min-w-0 flex-1">
                            <Link :href="route('catalog.show', item.book.slug)" class="text-title-md text-ink transition-colors hover:text-accent">
                                {{ item.book.title }}
                            </Link>
                            <p class="mt-space-xs text-body-sm text-muted">{{ item.book.author_name }}</p>
                            <div class="mt-space-sm flex flex-wrap items-center gap-space-xs">
                                <span class="inline-flex items-center gap-1 rounded-full bg-soft px-2.5 py-1 text-badge-label text-ink">
                                    <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                                    Lisensi Digital
                                </span>
                                <span class="rounded-full bg-soft px-2.5 py-1 text-badge-label text-muted">DRM Dilindungi</span>
                            </div>
                            <div class="mt-space-md flex flex-wrap items-center justify-between gap-space-sm border-t border-hairline-soft pt-space-sm">
                                <span class="text-action text-ink">{{ rupiah(item.book.effective_price) }}</span>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 text-caption text-muted transition-colors hover:text-error"
                                    @click="remove(item.book_id)"
                                >
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </article>
                </section>

                <aside class="lg:col-span-5">
                    <div class="sticky top-24 space-y-space-md rounded-[24px] border border-hair bg-soft p-space-lg">
                        <h2 class="text-title-md text-ink">Ringkasan Pesanan</h2>

                        <div class="space-y-space-sm rounded-2xl bg-canvas p-space-md">
                            <div v-for="item in items" :key="item.id" class="flex items-center justify-between gap-space-sm text-body-sm">
                                <span class="min-w-0 truncate text-muted">{{ item.book.title }}</span>
                                <span class="shrink-0 font-medium text-ink">{{ rupiah(item.book.effective_price) }}</span>
                            </div>
                        </div>

                        <div class="space-y-space-sm text-body-sm">
                            <div class="flex justify-between text-muted">
                                <span>Subtotal</span>
                                <span class="text-ink">{{ rupiah(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Biaya layanan</span>
                                <span class="text-ink">Gratis</span>
                            </div>
                            <div class="flex justify-between text-muted">
                                <span>Poin tersedia</span>
                                <span class="font-medium text-ink">{{ points_balance }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-hair pt-space-md">
                            <span class="text-title-md text-ink">Subtotal</span>
                            <span class="text-title-md text-ink">{{ rupiah(subtotal) }}</span>
                        </div>

                        <Link :href="route('checkout.index')" class="btn-primary w-full gap-2">
                            Lanjut ke Checkout
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </Link>

                        <div class="flex items-start gap-space-sm rounded-xl bg-field p-space-sm text-caption text-muted">
                            <span class="material-symbols-outlined text-[16px] text-ink">shield_lock</span>
                            <span>Pembayaran diproses lewat Midtrans. Berkas digital langsung tersedia setelah pembayaran terkonfirmasi.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
