<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="h text-3xl text-ink">Profilmu.</h1>
            <p class="lt mt-2 text-sm text-muted">
                Kelola informasi akun, kata sandi, dan keamanan.
            </p>
        </template>

        <div class="space-y-6">
            <div class="card">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="card">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="card">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
