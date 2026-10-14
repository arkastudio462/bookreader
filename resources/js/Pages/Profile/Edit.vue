<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const sections = [
    { id: 'profil', label: 'Informasi Profil', desc: 'Nama & alamat email', icon: 'person' },
    { id: 'keamanan', label: 'Kata Sandi', desc: 'Perbarui kata sandi', icon: 'lock' },
    { id: 'akun', label: 'Hapus Akun', desc: 'Tindakan permanen', icon: 'delete_forever' },
];
</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="h text-xl text-ink">Profil</h1>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Akun</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Profil</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Kelola Profil</h1>
                    <p class="text-body-md text-muted">Perbarui identitas, kata sandi, dan pengaturan keamanan akun Anda.</p>
                </div>
                <Link :href="route('dashboard')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Dasbor
                </Link>
            </header>

            <nav class="flex flex-wrap gap-space-xs" aria-label="Bagian profil">
                <a
                    v-for="section in sections"
                    :key="section.id"
                    :href="'#' + section.id"
                    class="inline-flex items-center gap-1.5 rounded-full bg-soft px-space-md py-1.5 text-body-sm text-ink transition-colors hover:bg-hair"
                >
                    <span class="material-symbols-outlined text-[16px] text-muted">{{ section.icon }}</span>
                    {{ section.label }}
                </a>
            </nav>

            <div class="space-y-space-lg">
                <section id="profil" class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                    <UpdateProfileInformationForm />
                </section>

                <section id="keamanan" class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                    <UpdatePasswordForm />
                </section>

                <section id="akun" class="rounded-[24px] border border-error/30 bg-canvas p-space-lg">
                    <DeleteUserForm />
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
