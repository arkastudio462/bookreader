<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <h1 class="h text-3xl text-ink">Masuk ke akunmu.</h1>
        <p class="lt mt-2 text-sm text-muted">
            Lanjutkan membaca, kelola pesanan, dan simpan progres bacamu.
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

            <div>
                <InputLabel for="password" value="Kata sandi" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between gap-3">
                <label class="flex items-center gap-2">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-sm text-muted">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="lk text-sm text-muted underline hover:text-ink"
                >
                    Lupa kata sandi?
                </Link>
            </div>

            <PrimaryButton
                class="w-full"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Masuk
            </PrimaryButton>
        </form>

        <p class="mt-6 text-center text-sm text-muted">
            Belum punya akun?
            <Link
                :href="route('register')"
                class="lk text-ink underline hover:text-ink-soft"
            >
                Daftar gratis
            </Link>
        </p>
    </GuestLayout>
</template>
