<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="rounded-t-md bg-ink px-6 pb-10 pt-16 text-white">
        <div class="mx-auto max-w-6xl">
            <p class="h text-6xl md:text-[80px]">PerpusKita.</p>
            <div class="mt-8 flex flex-wrap justify-between gap-10">
                <p class="max-w-xs text-faint">
                    Platform digital buku untuk pembaca, penulis, dan penerbit Indonesia.
                </p>
                <div class="grid grid-cols-2 gap-x-16 gap-y-3 text-sm">
                    <Link :href="route('landing')" class="lk text-white">Beranda</Link>
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
</template>
