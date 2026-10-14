<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="border-b border-hairline-soft pb-space-sm">
            <div class="flex items-center gap-space-sm">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-soft">
                    <span class="material-symbols-outlined text-[18px] text-ink">person</span>
                </span>
                <div>
                    <h2 class="text-title-md text-ink">Informasi profil</h2>
                    <p class="text-caption text-muted">Perbarui nama dan alamat email akunmu.</p>
                </div>
            </div>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-space-lg max-w-xl space-y-space-lg"
        >
            <div>
                <InputLabel for="name" value="Nama" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm text-muted">
                    Emailmu belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="lk text-ink underline hover:text-ink-soft"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="lk mt-2 text-sm text-ink"
                >
                    Tautan verifikasi baru sudah dikirim ke emailmu.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">
                    Simpan
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-body-sm text-muted"
                    >
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
