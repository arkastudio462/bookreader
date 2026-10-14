<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-space-lg">
        <header class="border-b border-hairline-soft pb-space-sm">
            <div class="flex items-center gap-space-sm">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-rose-50">
                    <span class="material-symbols-outlined text-[18px] text-error">delete_forever</span>
                </span>
                <div>
                    <h2 class="text-title-md text-ink">Hapus akun</h2>
                    <p class="text-caption text-muted">
                        Setelah akun dihapus, semua data dan sumber dayanya akan hilang permanen. Unduh dulu data yang ingin kamu simpan.
                    </p>
                </div>
            </div>
        </header>

        <DangerButton @click="confirmUserDeletion">Hapus akun</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="h text-xl text-ink">
                    Yakin ingin menghapus akunmu?
                </h2>

                <p class="mt-space-sm text-body-sm text-muted">
                    Setelah akun dihapus, semua data dan sumber dayanya akan
                    hilang permanen. Masukkan kata sandimu untuk mengonfirmasi
                    bahwa kamu benar-benar ingin menghapus akun ini.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Kata sandi"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Kata sandi"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Hapus akun
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
