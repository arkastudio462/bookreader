<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import BookCard from '@/Components/BookCard.vue';
import CoverImage from '@/Components/CoverImage.vue';
import FlashSaleCountdown from '@/Components/FlashSaleCountdown.vue';

const props = defineProps({
    book: Object,
    relatedBooks: Array,
    accessMode: { type: String, default: null },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const flashError = computed(() => page.props.flash?.error ?? null);
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const effectivePrice = computed(() => Number(props.book.effective_price ?? props.book.price));
const basePrice = computed(() => Number(props.book.base_price ?? props.book.price));
const formatNumber = (n) => Number(n).toLocaleString('id-ID');

function addToWishlist() {
    router.post(route('wishlist.store'), { book_id: props.book.id }, { preserveScroll: true });
}

function addToCart() {
    router.post(route('cart.store'), { book_id: props.book.id }, { preserveScroll: true });
}

const metaItems = computed(() => {
    const items = [];
    if (props.book.published_at) {
        items.push({
            label: 'Terbit',
            value: new Date(props.book.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }),
        });
    }
    if (props.book.language) {
        items.push({ label: 'Bahasa', value: props.book.language === 'id' ? 'Indonesia' : props.book.language });
    }
    if (props.book.pages) {
        items.push({ label: 'Halaman', value: props.book.pages });
    }
    if (props.book.isbn) {
        items.push({ label: 'ISBN', value: props.book.isbn });
    }
    return items;
});

const primaryAction = computed(() => {
    if (props.accessMode === 'full') {
        return { type: 'link', href: route('reader.show', props.book.id), label: 'Baca buku', class: 'btn-primary' };
    }
    if (props.accessMode === 'sample') {
        return { type: 'link', href: route('reader.show', props.book.id), label: 'Baca sampel gratis', class: 'btn-primary' };
    }
    if (props.user) {
        return { type: 'button', label: 'Tambah ke keranjang', class: 'btn-primary' };
    }
    return { type: 'link', href: route('login'), label: 'Beli sekarang', class: 'btn-primary' };
});

const secondaryAction = computed(() => {
    if (props.accessMode === 'sample') {
        return null;
    }
    if (props.user) {
        return { type: 'button', label: '+ Wishlist', class: 'btn-outline' };
    }
    return { type: 'link', href: route('login'), label: '+ Wishlist', class: 'btn-outline' };
});
</script>

<template>
    <Head :title="book.title" />

    <div class="flex min-h-screen flex-col bg-canvas">
        <AppHeader />

        <main class="mx-auto w-full max-w-6xl flex-1 px-5 pt-12">
            <!-- Breadcrumb -->
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <Link :href="route('catalog.index')" class="lk text-ink hover:underline">Katalog</Link>
                <span class="mx-2">/</span>
                <Link
                    v-if="book.category"
                    :href="route('catalog.index', { category: book.category.slug })"
                    class="lk text-ink hover:underline"
                >
                    {{ book.category.name }}
                </Link>
                <template v-if="book.category">
                    <span class="mx-2">/</span>
                </template>
                <span class="text-ink">{{ book.title }}</span>
            </nav>

            <div v-if="flashError" class="card-soft mt-6 text-sm text-ink">{{ flashError }}</div>
            <div v-if="flashSuccess" class="card-soft mt-6 text-sm text-ink">{{ flashSuccess }}</div>

            <div class="mt-8 grid gap-12 lg:grid-cols-[380px_1fr]">
                <!-- Cover -->
                <div class="mx-auto w-full max-w-[380px]">
                    <div class="relative lg:sticky lg:top-24">
                        <div class="absolute -inset-4 rounded-md bg-soft" aria-hidden="true"></div>
                        <div class="relative overflow-hidden rounded-sm shadow-2xl">
                            <img
                                v-if="book.cover_url"
                                :src="book.cover_url"
                                :alt="book.title"
                                class="aspect-[3/4] w-full object-cover"
                            />
                            <CoverImage v-else :book="book" />
                        </div>
                        <div v-if="book.discount_percent" class="absolute top-4 left-4">
                            <span class="badge-overlay !h-9 !px-4 !text-sm !bg-accent text-white">
                                Hemat {{ book.discount_percent }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="flex flex-col">
                    <div class="flex flex-wrap items-center gap-3">
                        <span v-if="book.category" class="badge-accent">{{ book.category.name }}</span>
                        <span v-if="book.on_promo" class="badge-accent !bg-ink">
                            {{ book.discount_source === 'flash' ? 'Flash Sale' : 'Sedang Promo' }}
                        </span>
                    </div>

                    <h1 class="h mt-4 text-4xl md:text-[44px]">{{ book.title }}</h1>
                    <p class="lt mt-3 text-xl text-muted">oleh {{ book.author_name }}</p>

                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted">
                        <span class="flex items-center gap-1.5">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-accent">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            <span class="lk text-ink">{{ book.average_rating }}</span>
                            <span>({{ formatNumber(book.rating_count) }} ulasan)</span>
                        </span>
                        <span v-if="book.sold_count">·</span>
                        <span v-if="book.sold_count">{{ formatNumber(book.sold_count) }} terjual</span>
                    </div>

                    <!-- Purchase Box -->
                    <div class="card-soft mt-8">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="h text-4xl">
                                {{ effectivePrice > 0 ? 'Rp ' + formatNumber(effectivePrice) : 'Gratis' }}
                            </span>
                            <span v-if="book.on_promo" class="text-lg text-faint line-through">
                                Rp {{ formatNumber(basePrice) }}
                            </span>
                        </div>

                        <!-- Hitung mundur flash sale (FR-4.7.9) -->
                        <div
                            v-if="book.discount_source === 'flash' && book.flash_ends_at"
                            class="mt-4 rounded-md bg-soft p-4"
                        >
                            <div class="flex flex-wrap items-center gap-4">
                                <span class="lk text-sm text-ink">Berakhir dalam</span>
                                <div class="[&_div]:!bg-canvas [&_div]:!text-ink">
                                    <FlashSaleCountdown :ends-at="book.flash_ends_at" />
                                </div>
                                <span
                                    v-if="book.flash_sold_percent !== null && book.flash_sold_percent !== undefined"
                                    class="text-sm text-muted"
                                >
                                    Terjual {{ book.flash_sold_percent }}%
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <Link
                                v-if="primaryAction.type === 'link'"
                                :href="primaryAction.href"
                                :class="[primaryAction.class, 'flex-1']"
                            >
                                {{ primaryAction.label }}
                            </Link>
                            <button
                                v-else
                                type="button"
                                :class="[primaryAction.class, 'flex-1']"
                                @click="addToCart"
                            >
                                {{ primaryAction.label }}
                            </button>

                            <button
                                v-if="secondaryAction && secondaryAction.type === 'button'"
                                type="button"
                                :class="secondaryAction.class"
                                @click="addToWishlist"
                            >
                                {{ secondaryAction.label }}
                            </button>
                            <Link
                                v-else-if="secondaryAction"
                                :href="secondaryAction.href"
                                :class="secondaryAction.class"
                            >
                                {{ secondaryAction.label }}
                            </Link>
                        </div>

                        <Link
                            v-if="accessMode === 'sample'"
                            :href="route('reader.show', book.id)"
                            class="btn-soft mt-3 w-full"
                        >
                            Baca sampel gratis
                        </Link>
                        <Link
                            v-else-if="!user"
                            :href="route('login')"
                            class="lk mt-3 block text-sm text-muted hover:text-ink hover:underline"
                        >
                            Masuk untuk membaca.
                        </Link>

                        <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-2 border-t border-hairline-soft pt-6 text-sm text-muted">
                            <li class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                                EPUB & PDF
                            </li>
                            <li class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                                Garansi 14 hari
                            </li>
                            <li class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                                Baca semua perangkat
                            </li>
                        </ul>
                    </div>

                    <!-- Synopsis -->
                    <div class="mt-10">
                        <h2 class="h text-2xl">Sinopsis</h2>
                        <p class="lt mt-3 leading-relaxed text-muted">{{ book.description }}</p>
                    </div>

                    <!-- Metadata -->
                    <dl v-if="metaItems.length" class="mt-8 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                        <div v-for="item in metaItems" :key="item.label" class="card-soft !p-4">
                            <dt class="text-muted">{{ item.label }}</dt>
                            <dd class="lk mt-1 text-ink">{{ item.value }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Related books -->
            <section v-if="relatedBooks.length" class="pt-24">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <h2 class="h text-3xl">Buku terkait.</h2>
                    <Link
                        v-if="book.category"
                        :href="route('catalog.index', { category: book.category.slug })"
                        class="lk text-sm text-accent hover:underline"
                    >
                        Lihat kategori ini →
                    </Link>
                </div>
                <div class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                    <BookCard v-for="b in relatedBooks" :key="b.id" :book="b" />
                </div>
            </section>
        </main>

        <AppFooter />
    </div>
</template>
