<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa kata sandi" />

        <h1 class="h text-3xl text-ink">Lupa kata sandi?</h1>
        <p class="lt mt-2 text-sm text-muted">
            Masukkan emailmu dan kami akan mengirim tautan untuk membuat kata
            sandi baru.
        </p>

        <div
            v-if="status"
            class="lk mt-4 rounded-sm bg-canvas px-4 py-3 text-sm text-ink"
        >
            {{ status }}
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <PrimaryButton
                class="w-full"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Kirim tautan reset
            </PrimaryButton>
        </form>

        <p class="mt-6 text-center text-sm text-muted">
            <Link
                :href="route('login')"
                class="lk text-ink underline hover:text-ink-soft"
            >
                Kembali ke halaman masuk
            </Link>
        </p>
    </GuestLayout>
</template>
