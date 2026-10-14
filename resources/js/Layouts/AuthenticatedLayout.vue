<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppMark from '@/Components/AppMark.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-white">
        <header class="sticky top-3 z-40 px-3">
            <nav
                class="mx-auto flex max-w-3xl items-center justify-between rounded-full bg-soft py-2 pl-3 pr-2"
                aria-label="Navigasi utama"
            >
                <Link :href="route('dashboard')" class="lk flex items-center gap-2 text-ink">
                    <AppMark />
                    <span class="hidden sm:inline">PerpusKita</span>
                </Link>
                <div class="hidden items-center gap-5 text-[15px] lk md:flex">
                    <Link :href="route('dashboard')" class="text-ink hover:underline">Dashboard</Link>
                    <Link :href="route('catalog.index')" class="text-ink hover:underline">Katalog</Link>
                    <Link :href="route('library.index')" class="text-ink hover:underline">Pustaka</Link>
                    <Link :href="route('wishlist.index')" class="text-ink hover:underline">Wishlist</Link>
                    <Link
                        v-if="page.props.auth.user.role === 'admin'"
                        :href="route('admin.flash-sale.index')"
                        class="text-ink hover:underline"
                    >
                        Flash Sale
                    </Link>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('cart.index')" class="lk inline-flex h-10 items-center rounded-full bg-white px-4 text-sm text-ink hover:bg-field">
                        Keranjang
                    </Link>
                    <div class="relative">
                        <button
                            class="lk inline-flex h-10 items-center gap-2 rounded-full bg-white px-3 text-sm text-ink hover:bg-field"
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                        >
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div
                            v-if="showingNavigationDropdown"
                            class="absolute right-0 mt-2 w-48 rounded-md border border-hairline-soft bg-canvas py-1"
                        >
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-ink hover:bg-soft">
                                Profil
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="block px-4 py-2 text-sm text-ink hover:bg-soft">
                                Keluar
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Flash Messages -->
        <div
            v-if="$page.props.flash?.success || $page.props.flash?.error"
            class="mx-auto max-w-3xl px-4 pt-4"
        >
            <div
                v-if="$page.props.flash.success"
                class="rounded-sm bg-soft px-4 py-3 text-sm text-ink"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                v-if="$page.props.flash.error"
                class="mt-2 rounded-sm bg-soft px-4 py-3 text-sm text-ink"
            >
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="mx-auto max-w-6xl px-5 pt-8">
            <slot name="header" />
        </header>

        <!-- Page Content -->
        <main class="mx-auto max-w-6xl px-5 py-8">
            <slot />
        </main>

        <footer class="rounded-t-md bg-ink px-6 pb-10 pt-16 text-white">
            <div class="mx-auto max-w-6xl">
                <p class="h text-4xl md:text-5xl">PerpusKita.</p>
                <div class="mt-8 flex flex-wrap justify-between gap-10">
                    <p class="max-w-xs text-faint">
                        Platform digital buku untuk pembaca, penulis, dan penerbit Indonesia.
                    </p>
                    <div class="grid grid-cols-2 gap-x-16 gap-y-3 text-sm">
                        <Link :href="route('dashboard')" class="lk text-white">Dashboard</Link>
                        <a href="#" class="text-faint hover:text-white">Instagram</a>
                        <Link :href="route('catalog.index')" class="lk text-white">Katalog</Link>
                        <a href="#" class="text-faint hover:text-white">YouTube</a>
                        <a href="#" class="text-faint hover:text-white">Syarat dan ketentuan</a>
                        <a href="#" class="text-faint hover:text-white">TikTok</a>
                        <a href="#" class="text-faint hover:text-white">Kebijakan privasi</a>
                        <a href="#" class="text-faint hover:text-white">X</a>
                    </div>
                </div>
                <p class="mt-12 text-xs text-faint">© 2026 PerpusKita. Semua hak dilindungi.</p>
            </div>
        </footer>
    </div>
</template>
