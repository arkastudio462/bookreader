<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi email" />

        <h1 class="h text-3xl text-ink">Verifikasi emailmu.</h1>
        <p class="lt mt-2 text-sm text-muted">
            Terima kasih sudah mendaftar! Klik tautan verifikasi yang kami kirim
            ke emailmu. Kalau belum menerima, kami bisa mengirim ulang.
        </p>

        <div
            v-if="verificationLinkSent"
            class="lk mt-4 rounded-sm bg-canvas px-4 py-3 text-sm text-ink"
        >
            Tautan verifikasi baru sudah dikirim ke email yang kamu daftarkan.
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <PrimaryButton
                class="w-full"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Kirim ulang email verifikasi
            </PrimaryButton>

            <p class="text-center text-sm text-muted">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="lk text-ink underline hover:text-ink-soft"
                >
                    Keluar
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
