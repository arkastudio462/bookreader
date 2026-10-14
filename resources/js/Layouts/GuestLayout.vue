<script setup>
import { Link } from '@inertiajs/vue3';
import AppMark from '@/Components/AppMark.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas">
        <header class="sticky top-3 z-40 px-3">
            <nav
                class="mx-auto flex max-w-3xl items-center justify-between rounded-full border border-hairline-soft bg-canvas py-2 pl-3 pr-2"
                aria-label="Navigasi utama"
            >
                <Link :href="route('landing')" class="lk flex items-center gap-2 text-ink">
                    <AppMark />
                    <span class="hidden sm:inline">PerpusKita</span>
                </Link>
                <div class="flex items-center gap-2">
                    <Link :href="route('login')" class="lk hidden h-10 items-center px-4 text-sm text-ink hover:underline sm:inline-flex">
                        Masuk
                    </Link>
                    <Link :href="route('register')" class="lk inline-flex h-10 items-center rounded-full bg-ink px-4 text-sm text-white hover:bg-ink-soft">
                        Daftar gratis
                    </Link>
                </div>
            </nav>
        </header>

        <main class="flex flex-1 flex-col items-center justify-center px-5 py-10">
            <div class="w-full max-w-md">
                <div class="card-soft">
                    <slot />
                </div>
            </div>
        </main>

        <footer class="px-5 pb-8 text-center text-xs text-faint">
            © 2026 PerpusKita. Semua hak dilindungi.
        </footer>
    </div>
</template>
