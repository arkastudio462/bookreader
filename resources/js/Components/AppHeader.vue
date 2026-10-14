<script setup>
import { Link } from '@inertiajs/vue3';
import AppMark from '@/Components/AppMark.vue';
</script>

<template>
    <header class="sticky top-3 z-40 px-3">
        <nav
            class="mx-auto flex max-w-3xl items-center justify-between rounded-full bg-soft py-2 pl-3 pr-2"
            aria-label="Navigasi utama"
        >
            <Link :href="route('landing')" class="lk flex items-center gap-2 text-ink">
                <AppMark />
                <span class="hidden sm:inline">PerpusKita</span>
            </Link>
            <div class="hidden items-center gap-6 text-[15px] lk md:flex">
                <Link :href="route('catalog.index')" class="text-ink hover:underline">Katalog</Link>
                <Link :href="route('catalog.index', { promo: 1 })" class="text-ink hover:underline">Flash sale</Link>
                <Link :href="route('login')" class="text-ink hover:underline">Masuk</Link>
            </div>
            <div class="flex items-center gap-2">
                <Link :href="route('register')" class="lk inline-flex h-10 items-center rounded-full bg-ink px-4 text-sm text-white hover:bg-ink-soft">
                    Daftar gratis
                </Link>
                <button
                    id="menuBtn"
                    class="grid h-10 w-10 place-items-center rounded-full bg-white text-ink md:hidden"
                    aria-label="Buka menu"
                    :aria-expanded="menuOpen"
                    @click="menuOpen = !menuOpen"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path v-if="!menuOpen" d="M4 8h16M4 16h16" />
                        <path v-else d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>
        <div
            v-if="menuOpen"
            class="mx-auto mt-2 flex max-w-3xl flex-col gap-1 rounded-md bg-soft p-3 lk md:hidden"
        >
            <Link :href="route('catalog.index')" class="rounded-full px-4 py-3 text-ink hover:bg-field">Katalog</Link>
            <Link :href="route('catalog.index', { promo: 1 })" class="rounded-full px-4 py-3 text-ink hover:bg-field">Flash sale</Link>
            <Link :href="route('login')" class="rounded-full px-4 py-3 text-ink hover:bg-field">Masuk</Link>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return { menuOpen: false };
    },
};
</script>
