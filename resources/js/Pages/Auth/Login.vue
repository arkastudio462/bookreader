<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const submitting = ref(false);

const submit = () => {
    submitting.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            submitting.value = false;
        },
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Masuk" />

        <div class="mx-auto w-full max-w-[1440px] px-margin-lg py-space-xl">
            <div class="grid grid-cols-1 gap-gutter-lg lg:grid-cols-12">
                <section class="flex flex-col justify-between lg:col-span-7 lg:pr-space-xl">
                    <div class="w-full max-w-[560px]">
                        <div class="mb-space-xl">
                            <span class="mb-space-sm inline-flex items-center gap-space-xs text-muted text-caption uppercase tracking-widest select-none">
                                <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                                Akses Portal Pembaca & Penulis
                            </span>
                            <h1 class="mb-space-sm text-headline-sm md:text-headline-md lg:text-headline-lg text-ink tracking-tight">
                                Selamat datang kembali.
                            </h1>
                            <p class="text-body-lead text-body-md md:text-body-lead text-muted">
                                Masuk untuk melanjutkan membaca karya penulis favorit atau mengelola studio royalti Anda secara transparan.
                            </p>
                        </div>

                        <div class="mb-space-lg grid grid-cols-1 gap-space-md sm:grid-cols-2">
                            <button type="button" class="flex h-12 items-center justify-center gap-space-sm rounded-full border border-hair bg-canvas text-ink transition-colors hover:bg-soft">
                                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"></path>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"></path>
                                </svg>
                                <span>Google</span>
                            </button>
                            <button type="button" class="flex h-12 items-center justify-center gap-space-sm rounded-full border border-hair bg-canvas text-ink transition-colors hover:bg-soft">
                                <svg class="h-5 w-5 flex-shrink-0 fill-current text-ink" viewBox="0 0 24 24">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.38c.62-.75 1.04-1.8.93-2.85-.9.04-1.99.6-2.64 1.35-.58.65-1.09 1.72-.95 2.73 1.01.08 2.04-.48 2.66-1.23"></path>
                                </svg>
                                <span>Apple</span>
                            </button>
                        </div>

                        <div class="relative my-space-lg flex items-center">
                            <div class="h-px flex-grow bg-hairline-soft"></div>
                            <span class="mx-space-md flex-shrink text-faint text-caption select-none">
                                atau lanjutkan dengan email
                            </span>
                            <div class="h-px flex-grow bg-hairline-soft"></div>
                        </div>

                        <form class="flex flex-col gap-space-lg" @submit.prevent="submit">
                            <div class="flex flex-col gap-space-xs">
                                <label for="email" class="flex items-center justify-between text-[14px] text-ink font-semibold">
                                    <span>Alamat Email</span>
                                    <span class="text-faint text-caption font-normal">Wajib diisi</span>
                                </label>
                                <div class="relative">
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="nama@domain.id"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="h-12 w-full rounded bg-field pr-12 text-body-md text-ink outline-none transition-all placeholder:text-faint focus:bg-canvas focus:ring-2 focus:ring-ink"
                                    />
                                    <span class="material-symbols-outlined pointer-events-none absolute right-4 top-3 text-[20px] text-faint">alternate_email</span>
                                </div>
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="flex flex-col gap-space-xs">
                                <label for="password" class="flex items-center justify-between text-[14px] text-ink font-semibold">
                                    <span>Kata Sandi</span>
                                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-muted text-caption transition-colors hover:text-accent">
                                        Lupa kata sandi?
                                    </Link>
                                </label>
                                <div class="relative flex items-center">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        placeholder="••••••••••••"
                                        required
                                        autocomplete="current-password"
                                        class="h-12 w-full rounded bg-field pr-12 text-body-md text-ink outline-none transition-all placeholder:text-faint focus:bg-canvas focus:ring-2 focus:ring-ink"
                                    />
                                    <button type="button" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-3 flex items-center justify-center rounded-full p-1.5 text-muted transition-colors hover:text-ink" @click="showPassword = !showPassword">
                                        <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                                    </button>
                                </div>
                                <InputError :message="form.errors.password" />
                            </div>

                            <div class="flex items-center justify-between pt-space-xs">
                                <label class="flex cursor-pointer select-none items-center gap-space-sm">
                                    <input v-model="form.remember" type="checkbox" class="h-4 w-4 cursor-pointer rounded border-0 bg-field accent-ink transition focus:ring-2 focus:ring-ink" />
                                    <span class="text-body-sm text-ink-soft">Ingat saya selama 30 hari</span>
                                </label>
                            </div>

                            <button type="submit" :disabled="form.processing" class="mt-space-sm flex h-12 w-full items-center justify-center gap-space-sm rounded-full bg-ink text-canvas transition-all hover:opacity-90 active:scale-[0.99] disabled:opacity-50">
                                <span v-if="!form.processing">Masuk ke Akun</span>
                                <span v-else>Memverifikasi...</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </form>

                        <div class="mt-space-xl flex flex-col items-center gap-space-xs pt-space-md text-center sm:flex-row sm:text-left">
                            <span class="text-body-sm text-muted">Belum memiliki akun pembaca atau kreator?</span>
                            <Link :href="route('register')" class="inline-flex items-center gap-0.5 text-action text-ink font-semibold transition-colors hover:text-accent">
                                <span>Daftar sekarang gratis</span>
                                <span class="material-symbols-outlined text-[16px]">trending_flat</span>
                            </Link>
                        </div>

                        <div class="mt-space-xl flex items-center gap-space-md rounded bg-soft p-space-md">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-canvas text-ink">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[13px] text-ink font-semibold">Perlindungan Hak Cipta & Sesi Aman</span>
                                <p class="text-caption text-muted">
                                    Dienkripsi dengan standar TLS 1.3 dan LCP EPUB 3 DRM. Karya digital Anda terlindungi secara hukum literasi nasional.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="hidden pt-space-xl text-faint text-caption lg:block">
                        PerpusKita ID — Standar Kurasi Digital Terbuka untuk Sastra Asia Tenggara.
                    </div>
                </section>

                <aside class="hidden lg:col-span-5 lg:flex">
                    <div class="flex w-full flex-col justify-between rounded-lg bg-soft p-space-lg md:p-space-xl">
                        <div class="flex items-center justify-between pb-space-lg">
                            <div class="inline-flex items-center gap-space-xs rounded-full bg-canvas px-space-md py-1 shadow-sm">
                                <span class="h-2 w-2 animate-pulse rounded-full bg-accent"></span>
                                <span class="text-badge-label text-ink">PILIHAN KURATOR BULAN INI</span>
                            </div>
                            <span class="text-caption font-semibold tracking-wider text-muted uppercase">EPUB 3 • DRM</span>
                        </div>

                        <div class="my-space-md rounded bg-canvas p-space-lg shadow-sm ring-1 ring-hairline-soft">
                            <div class="mb-space-lg flex items-start gap-space-md">
                                <div class="relative h-28 w-20 flex-shrink-0 overflow-hidden rounded bg-field shadow-md">
                                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuD8wNq-S20iSAqCG9Fjwp0IUwEETrL8UZ9FLBho_v7qzeMVpBi9C5lJi1Xva_4hiJv7twvrWni4n5ScLkxLBn1k7vq9by1gorQKQaXKwHX1NzcAG6xeKNyYtviskP1t2p5NpCuYcNc38QrF9BeNlmIC9sEBkI_eZ6E7gz19u3sWsX5U3qqVQQ0HqgK-Y1eXk2xHVCK3VnIlzQCG0O4EcxvoQYRhGgfEzvZeRMjAvO5uUD0hlSVhvpXrcA" alt="Hujan di Atas Kota Lama" class="h-full w-full object-cover" />
                                    <div class="absolute bottom-1 right-1 rounded-full bg-scrim px-1.5 py-0.5 text-[9px] tracking-tight text-canvas backdrop-blur-sm">184 Hal</div>
                                </div>
                                <div class="flex min-w-0 flex-col justify-between">
                                    <div>
                                        <span class="text-accent text-caption font-semibold tracking-wider uppercase">Fiksi Sejarah</span>
                                        <h3 class="mt-0.5 truncate text-title-md text-ink">Hujan di Atas Kota Lama</h3>
                                        <p class="text-body-sm text-muted">Arya Wirawan</p>
                                    </div>
                                    <div class="mt-space-sm flex items-center gap-1.5">
                                        <div class="flex text-ink">
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                        </div>
                                        <span class="text-caption font-semibold text-muted">4.92 / 5.0</span>
                                    </div>
                                </div>
                            </div>
                            <blockquote class="mb-space-md text-[17px] leading-relaxed text-ink italic">
                                "Di antara batu bata merah benteng tua dan aroma tanah basah, kota itu tidak pernah benar-benar tidur. Ia hanya berganti suara."
                            </blockquote>
                            <div class="flex items-center justify-between border-t border-hairline-soft pt-space-sm text-muted">
                                <span class="text-caption">Bab IV: Melodi Genting Seng</span>
                                <span class="text-caption text-faint">Catatan Kurasi Litera Nusantara</span>
                            </div>
                        </div>

                        <div class="mb-space-lg flex items-center gap-space-md rounded bg-canvas p-space-md ring-1 ring-hairline-soft">
                            <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full bg-field">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDNA7u7y5bSVZVM4utf1tlAVGL2u_Bof6dw1nxey1PFAkBjlnt4FUjMvdPkN16QpsxnG-FhEhYROc-33ow9H697M-xIw-jc7nhHl8bQ3ZVy8EgZeZFftHTOnrxkQoSjRb9IihndBl-YbPj-SxWjum_rPjZzy9Nq96y4wj4DKOzAfBH9Rv4IMhNlSCx4JvUjABAx0RTkCNnC83ncrLjzWScoSUIV2lesnCw18AeeOKWuOL1BxyImMCQfZg" alt="Dian Sastrowardoyo" class="h-full w-full object-cover" />
                            </div>
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-body-sm text-ink-soft">
                                    "Pengalaman membaca paling murni tanpa banner iklan. Tata letak tipografi di PerpusKita serasa memegang buku cetak luks."
                                </p>
                                <span class="mt-0.5 text-caption text-muted">Dian Sastrowardoyo • Pembaca Terverifikasi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-space-sm border-t border-hairline-soft pt-space-md">
                            <div class="flex flex-col">
                                <span class="text-title-md font-semibold text-ink">89k+</span>
                                <span class="text-caption text-muted">Pembaca Aktif</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-title-md font-semibold text-ink">1.400+</span>
                                <span class="text-caption text-muted">Karya Kurasi</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-title-md font-semibold text-accent">100%</span>
                                <span class="text-caption text-muted">Royalti Transparan</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AuthLayout>
</template>
