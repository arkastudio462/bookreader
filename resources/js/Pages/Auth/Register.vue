<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'reader',
});

const showPassword = ref(false);

const roleDescriptions = {
    reader: 'Beli & baca buku, sinkronisasi lintas perangkat, anotasi catatan tanpa batas.',
    writer: 'Terbitkan naskah mandiri, dashboard royalti real-time 70%, dan proteksi lisensi.',
};

const roleHints = {
    reader: 'Akses penuh pembaca',
    writer: 'Akses penulis & penerbit',
};

const passwordChecks = computed(() => ({
    length: form.password.length >= 8,
    combo: /[a-zA-Z]/.test(form.password) && /[0-9]/.test(form.password),
}));

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Daftar" />

        <div class="mx-auto w-full max-w-[1440px] px-margin-lg py-space-xl">
            <div class="grid grid-cols-1 gap-gutter-lg lg:grid-cols-12">
                <section class="flex flex-col lg:col-span-7">
                    <div class="mb-space-lg flex items-center justify-between border-b border-hair pb-space-lg">
                        <div class="flex items-center gap-space-sm">
                            <div class="grid h-9 w-9 place-items-center rounded bg-ink">
                                <span class="text-[17px] font-bold leading-none text-canvas select-none">P</span>
                            </div>
                            <span class="text-title-md font-semibold tracking-tight text-ink">PerpusKita.</span>
                        </div>
                        <Link :href="route('login')" class="inline-flex items-center gap-space-xs text-action text-muted transition-colors hover:text-ink">
                            <span>Sudah punya akun? Masuk</span>
                            <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">north_east</span>
                        </Link>
                    </div>

                    <div class="mb-space-xl">
                        <h1 class="mb-space-sm text-headline-md tracking-tight text-ink">
                            Mulai perjalanan membacamu.
                        </h1>
                        <p class="max-w-xl text-body-lead text-muted">
                            Buka akses ke ribuan literatur pilihan nusantara, catatan sinkronisasi cloud, dan studio karya terintegrasi.
                        </p>
                    </div>

                    <div class="mb-space-xl">
                        <div class="mb-space-sm flex items-center justify-between">
                            <label class="text-badge-label tracking-wider text-ink uppercase">Pilih Peran Akun</label>
                            <span class="text-caption text-muted">{{ roleHints[form.role] }}</span>
                        </div>
                        <div class="grid grid-cols-1 gap-space-sm rounded-full bg-soft p-space-xs sm:grid-cols-2">
                            <button
                                type="button"
                                class="flex items-center justify-center gap-space-sm rounded-full px-space-md py-space-sm text-action transition-all"
                                :class="form.role === 'reader' ? 'bg-ink text-white' : 'text-muted hover:text-ink'"
                                @click="form.role = 'reader'"
                            >
                                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                                <span>Pembaca Digital</span>
                            </button>
                            <button
                                type="button"
                                class="flex items-center justify-center gap-space-sm rounded-full px-space-md py-space-sm text-action transition-all"
                                :class="form.role === 'writer' ? 'bg-ink text-white' : 'text-muted hover:text-ink'"
                                @click="form.role = 'writer'"
                            >
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                <span>Penulis / Penerbit</span>
                            </button>
                        </div>
                        <div class="mt-space-sm flex items-center gap-space-sm rounded bg-soft px-space-md py-space-sm text-muted text-body-sm">
                            <span class="material-symbols-outlined text-[18px] text-accent">verified</span>
                            <span>{{ roleDescriptions[form.role] }}</span>
                        </div>
                    </div>

                    <div class="mb-space-xl grid grid-cols-1 gap-space-md sm:grid-cols-2">
                        <button type="button" class="flex h-12 items-center justify-center gap-space-sm rounded-full border border-hair bg-canvas text-ink transition-colors hover:bg-soft">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" fill="#4285F4"></path>
                                <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.35 24 12 24z" fill="#34A853"></path>
                                <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
                                <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" fill="#EA4335"></path>
                            </svg>
                            <span>Daftar dengan Google</span>
                        </button>
                        <button type="button" class="flex h-12 items-center justify-center gap-space-sm rounded-full border border-hair bg-canvas text-ink transition-colors hover:bg-soft">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.84c.66-.82 1.11-1.96.99-3.1-.96.04-2.12.64-2.8 1.44-.6.69-1.12 1.85-.98 2.95 1.07.08 2.13-.53 2.79-1.29"></path>
                            </svg>
                            <span>Daftar dengan Apple</span>
                        </button>
                    </div>

                    <div class="relative mb-space-xl flex items-center justify-center">
                        <div class="w-full border-t border-hair"></div>
                        <span class="absolute bg-canvas px-space-md text-caption tracking-wider text-muted uppercase">
                            atau daftar manual
                        </span>
                    </div>

                    <form class="mb-space-xl space-y-space-lg" @submit.prevent="submit">
                        <div>
                            <label for="name" class="mb-space-xs block text-badge-label tracking-wider text-ink uppercase">Nama Lengkap</label>
                            <div class="relative">
                                <input id="name" v-model="form.name" type="text" placeholder="misal: Rian Sastrawan" required autofocus autocomplete="name" class="h-12 w-full rounded bg-field pr-12 text-body-md text-ink outline-none transition-all placeholder:text-faint focus:ring-2 focus:ring-ink" />
                                <span class="material-symbols-outlined pointer-events-none absolute right-4 top-3 text-[20px] text-faint">person</span>
                            </div>
                            <InputError :message="form.errors.name" />
                        </div>

                        <div>
                            <label for="email" class="mb-space-xs block text-badge-label tracking-wider text-ink uppercase">Alamat Email</label>
                            <div class="relative">
                                <input id="email" v-model="form.email" type="email" placeholder="pembaca@perpuskita.id" required autocomplete="username" class="h-12 w-full rounded bg-field pr-12 text-body-md text-ink outline-none transition-all placeholder:text-faint focus:ring-2 focus:ring-ink" />
                                <span class="material-symbols-outlined pointer-events-none absolute right-4 top-3 text-[20px] text-faint">alternate_email</span>
                            </div>
                            <InputError :message="form.errors.email" />
                        </div>

                        <div>
                            <div class="mb-space-xs flex items-center justify-between">
                                <label for="password" class="block text-badge-label tracking-wider text-ink uppercase">Kata Sandi</label>
                                <button type="button" class="text-caption text-muted transition-colors hover:text-ink" @click="showPassword = !showPassword">
                                    {{ showPassword ? 'Sembunyikan' : 'Lihat sandi' }}
                                </button>
                            </div>
                            <div class="relative">
                                <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Buat kombinasi aman" required autocomplete="new-password" class="h-12 w-full rounded bg-field pr-12 text-body-md text-ink outline-none transition-all placeholder:text-faint focus:ring-2 focus:ring-ink" />
                                <span class="material-symbols-outlined pointer-events-none absolute right-4 top-3 text-[20px] text-faint">lock</span>
                            </div>
                            <div class="mt-space-sm grid grid-cols-1 gap-space-xs rounded bg-soft p-space-sm text-caption text-muted sm:grid-cols-2">
                                <div class="flex items-center gap-space-xs transition-colors" :class="{ 'text-ink': passwordChecks.length }">
                                    <span class="material-symbols-outlined text-[16px]" :class="passwordChecks.length ? 'text-accent' : 'text-faint'">{{ passwordChecks.length ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                    <span>Minimal 8 karakter</span>
                                </div>
                                <div class="flex items-center gap-space-xs transition-colors" :class="{ 'text-ink': passwordChecks.combo }">
                                    <span class="material-symbols-outlined text-[16px]" :class="passwordChecks.combo ? 'text-accent' : 'text-faint'">{{ passwordChecks.combo ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                    <span>Kombinasi huruf & angka</span>
                                </div>
                            </div>
                            <InputError :message="form.errors.password" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-space-xs block text-badge-label tracking-wider text-ink uppercase">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Ulangi kata sandi" required autocomplete="new-password" class="h-12 w-full rounded bg-field text-body-md text-ink outline-none transition-all placeholder:text-faint focus:ring-2 focus:ring-ink" />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-start gap-space-sm pt-space-xs">
                            <input id="terms" required type="checkbox" class="mt-0.5 h-5 w-5 cursor-pointer rounded accent-ink focus:ring-0" />
                            <label for="terms" class="cursor-pointer text-body-sm text-muted select-none">
                                Saya menyetujui <a href="#" class="font-semibold text-ink underline transition-colors hover:text-accent">Ketentuan Layanan</a> dan <a href="#" class="font-semibold text-ink underline transition-colors hover:text-accent">Kebijakan Privasi</a> PerpusKita.
                            </label>
                        </div>

                        <button type="submit" :disabled="form.processing" class="flex h-12 w-full items-center justify-center gap-space-sm rounded-full bg-ink text-white transition-all hover:opacity-90 active:scale-[0.99] disabled:opacity-50">
                            <span>Buat Akun PerpusKita</span>
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </form>

                    <div class="flex items-start gap-space-md rounded bg-soft p-space-md text-muted">
                        <span class="material-symbols-outlined mt-0.5 shrink-0 text-ink text-[22px]">verified_user</span>
                        <p class="text-caption leading-relaxed">
                            <strong class="font-semibold text-ink">Komitmen Koleksi & Privasi:</strong> Seluruh karya yang Anda beli terikat kepemilikan sejati bebas DRM tertutup. Tersedia jaminan pengembalian dana saldo 7 hari jika pengalaman membaca tidak memenuhi ekspektasi Anda.
                        </p>
                    </div>
                </section>

                <aside class="hidden rounded-lg bg-soft p-space-lg lg:col-span-5 lg:block lg:p-space-xl">
                    <div>
                        <div class="mb-space-lg inline-flex items-center gap-space-xs rounded-full bg-ink px-space-md py-1 text-canvas text-badge-label tracking-widest uppercase">
                            <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                            <span>Standar Baru Membaca</span>
                        </div>
                        <h2 class="mb-space-sm text-title-lg font-semibold tracking-tight text-ink">
                            Ruang literasi tanpa distorsi.
                        </h2>
                        <p class="mb-space-xl text-body-md text-muted">
                            Dirancang seperti galeri seni: latar tenang yang memberi panggung seutuhnya pada kedalaman teks dan estetika perwajahan buku.
                        </p>

                        <div class="mb-space-xl space-y-space-md">
                            <div class="flex items-start gap-space-md rounded bg-canvas p-space-md">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-soft text-ink">
                                    <span class="material-symbols-outlined text-[20px]">key_off</span>
                                </div>
                                <div>
                                    <h3 class="mb-0.5 text-[16px] font-semibold text-ink">Bebas DRM & Hak Milik Permanen</h3>
                                    <p class="text-caption leading-relaxed text-muted">Unduh format EPUB 3 responsif. Baca di ereader favorit Anda kapan pun tanpa ancaman perpustakaan terhapus otomatis.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-space-md rounded bg-canvas p-space-md">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-soft text-ink">
                                    <span class="material-symbols-outlined text-[20px]">chrome_reader_mode</span>
                                </div>
                                <div>
                                    <h3 class="mb-0.5 text-[16px] font-semibold text-ink">Web Reader Tanpa Iklan</h3>
                                    <p class="text-caption leading-relaxed text-muted">Pengalaman membaca murni dengan tipografi Saans, kontrol suhu warna, dan sinkronisasi awan.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-space-md rounded bg-canvas p-space-md">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-soft text-ink">
                                    <span class="material-symbols-outlined text-[20px]">handshake</span>
                                </div>
                                <div>
                                    <h3 class="mb-0.5 text-[16px] font-semibold text-ink">Dukungan 70% Royalti Penulis</h3>
                                    <p class="text-caption leading-relaxed text-muted">Setiap pembelian langsung mengalirkan porsi terbesar kepada penulis lokal independen dan kurator sastra nusantara.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-space-xl">
                        <div class="mb-space-sm flex items-center justify-between">
                            <span class="text-badge-label tracking-wider text-muted uppercase">Koleksi Minggu Ini</span>
                            <span class="text-caption font-semibold text-accent">1,420+ Rilis Baru</span>
                        </div>
                        <div class="grid grid-cols-3 gap-space-sm">
                            <div class="group relative aspect-[2/3] overflow-hidden rounded bg-canvas">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3iT8kU7Uvs2sgG724VeamPZ1q1A1_WSeiR-m2mRQ9jJJClOzJzaPAdlijNxDmhYOiHjFvTa6NLfyqkkWIcNO7T6x6sqbTEUReO9ETMYvA0FNd5zKnMqZY4J92lb1v1IMorJ8Sow56CmX7kK-cR085ouqvx0NrKf6d1YodbDaxcOyC68Bmq4BTgcE6BlEewNeieS9B_FHq0vOspxilcaxrBFdGuzHCWvHTdPyQB900EBKxBI9cXbs5dw" alt="Peta Nusantara Lama" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
                                <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-ink/80 via-transparent to-transparent p-2 text-white">
                                    <span class="text-[10px] font-semibold leading-tight line-clamp-1">Peta Nusantara</span>
                                    <span class="text-[9px] text-canvas opacity-80">Sejarah</span>
                                </div>
                            </div>
                            <div class="group relative aspect-[2/3] overflow-hidden rounded bg-canvas">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7kkOzN6PnrT91Rfj15abRr5JAU5lXOlKrPD0ZcyAKXp80mYrktJr__TAraaPRdGBE9falkhbLio5JsOWm4CbgTvlay_Cm7_0fuJfiNo5EjFEGK3IP5X5gMziybGS3hpZsNL4339Y6KboTkAYU4Dz1ybnC4Sf6FwKQmu-z_cTxPrPMAockkOglubSIEBzAS4H_ZXdpqval6uEdF6ONwZs9qV1AxmerKJ2EUKlW6nSZPxZUWcUPzujpwQ" alt="Malam di Malioboro" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
                                <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-ink/80 via-transparent to-transparent p-2 text-white">
                                    <span class="text-[10px] font-semibold leading-tight line-clamp-1">Malam di Malioboro</span>
                                    <span class="text-[9px] text-canvas opacity-80">Fiksi</span>
                                </div>
                            </div>
                            <div class="group relative aspect-[2/3] overflow-hidden rounded bg-canvas">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsoAXa3Dhp7D7Wt0DFkNt5n8_bT3XfX8vQAZQlTYFwXcXsgIYMI6SttHl1jlsVZ_OyAZrHjNe-0ekWWHegCeuHrsAALeS-ybg4cTxu4pEv6rH4v1W_osPiiZHl0lhyg_mPp4WlxmgsW2PJVRAyC1glmGP-NK3tBdyX74OpDJ7wKQJw0rJffJpo5Gk_rYecaySDcbKMmROTM79ayT02GbIhkGo_CwDPyIXbvkc6ptwEswknA6ABgC3xtA" alt="Filosofi Teras Kota" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
                                <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-ink/80 via-transparent to-transparent p-2 text-white">
                                    <span class="text-[10px] font-semibold leading-tight line-clamp-1">Filosofi Teras</span>
                                    <span class="text-[9px] text-canvas opacity-80">Pemikiran</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-sm border-t border-hair pt-space-md">
                        <blockquote class="text-body-sm leading-relaxed text-ink italic">
                            "PerpusKita mengembalikan kesucian hubungan antara pembaca dan gagasan penulis, tanpa bising iklan atau restriksi sepihak."
                        </blockquote>
                        <div class="flex items-center justify-between pt-space-xs">
                            <div class="flex items-center gap-space-sm">
                                <div class="grid h-8 w-8 place-items-center rounded-full bg-field text-caption font-semibold text-ink">AK</div>
                                <div>
                                    <p class="text-badge-label text-ink">Ayu Kartika</p>
                                    <p class="text-caption text-muted">Kurator Dewan Buku Jakarta</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-ink">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="text-badge-label">4.9/5</span>
                                <span class="text-caption text-muted">(18rb ulasan)</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AuthLayout>
</template>
