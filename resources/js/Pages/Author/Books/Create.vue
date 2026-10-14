<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
    genres: Array,
});

const form = useForm({
    title: '',
    description: '',
    author_name: '',
    isbn: '',
    category_id: '',
    genre_id: '',
    language: 'id',
    price: '',
    sample_price: '',
    pages: '',
    cover: null,
    file: null,
});

const coverPreview = ref('');
const filePreview = ref('');

function handleCoverChange(e) {
    const file = e.target.files[0];
    if (file) {
        form.cover = file;
        coverPreview.value = URL.createObjectURL(file);
    }
}

function handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
        form.file = file;
        filePreview.value = file.name;
    }
}

function submit() {
    form.post(route('author.books.store'), {
        onError: () => {},
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Unggah Buku - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Unggah Buku Baru</h2>
        </template>

        <div class="max-w-4xl space-y-space-lg">
            <header class="border-b border-hair pb-space-lg">
                <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                    <Link :href="route('author.books.index')" class="transition-colors hover:text-ink">Kelola Buku</Link>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="font-semibold text-ink">Unggah Baru</span>
                </nav>
                <h1 class="mt-space-xs text-headline-sm tracking-tight text-ink">Unggah Buku Baru</h1>
                <p class="mt-space-xs text-body-md text-muted">Lengkapi detail naskah, unggah sampul, dan kirim berkas EPUB/PDF.</p>
            </header>
                <form class="space-y-space-lg" @submit.prevent="submit">
                    <div class="space-y-space-md rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <h3 class="border-b border-hairline-soft pb-space-sm text-title-md text-ink">Informasi Dasar</h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-body-sm text-ink">Judul *</label>
                                <input v-model="form.title" type="text" class="field mt-1 block w-full">
                                <span v-if="form.errors.title" class="mt-1 text-caption text-error">{{ form.errors.title }}</span>
                            </div>

                            <div>
                                <label class="block text-body-sm text-ink">ISBN</label>
                                <input v-model="form.isbn" type="text" class="field mt-1 block w-full">
                            </div>

                            <div>
                                <label class="block text-body-sm text-ink">Nama Penulis *</label>
                                <input v-model="form.author_name" type="text" class="field mt-1 block w-full">
                            </div>

                            <div>
                                <label class="block text-body-sm text-ink">Kategori *</label>
                                <select v-model="form.category_id" class="field mt-1 block w-full">
                                    <option value="">Pilih Kategori</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-body-sm text-ink">Bahasa *</label>
                                <select v-model="form.language" class="field mt-1 block w-full">
                                    <option value="id">Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-body-sm text-ink">Harga Jual (Rp) *</label>
                                <input v-model.number="form.price" type="number" class="field mt-1 block w-full">
                                <span v-if="form.errors.price" class="mt-1 text-caption text-error">{{ form.errors.price }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-body-sm text-ink">Deskripsi *</label>
                            <textarea v-model="form.description" rows="4" class="field mt-1 block w-full"></textarea>
                            <span v-if="form.errors.description" class="mt-1 text-caption text-error">{{ form.errors.description }}</span>
                        </div>
                    </div>

                    <div class="space-y-space-md rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <h3 class="border-b border-hairline-soft pb-space-sm text-title-md text-ink">File & Cover</h3>

                        <div>
                            <label class="block text-body-sm text-ink">Cover Buku *</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <div v-if="coverPreview" class="h-24 w-16 overflow-hidden rounded-sm">
                                    <img :src="coverPreview" alt="Cover Preview" class="h-full w-full object-cover">
                                </div>
                                <input @change="handleCoverChange" type="file" accept="image/*" class="block w-full text-sm text-muted">
                            </div>
                            <span v-if="form.errors.cover" class="mt-1 text-caption text-error">{{ form.errors.cover }}</span>
                        </div>

                        <div>
                            <label class="block text-body-sm text-ink">File Buku (EPUB/PDF) *</label>
                            <div class="mt-1 flex items-center">
                                <span v-if="filePreview" class="mr-4 text-sm text-ink-soft">{{ filePreview }}</span>
                                <input @change="handleFileChange" type="file" accept=".epub,.pdf" class="block w-full text-sm text-muted">
                            </div>
                            <span v-if="form.errors.file" class="mt-1 text-caption text-error">{{ form.errors.file }}</span>
                        </div>
                    </div>

                    <div class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <div class="flex justify-end gap-4">
                            <Link :href="route('author.books.index')" class="btn-outline">
                                Batal
                            </Link>
                            <button type="submit" class="btn-primary">
                                Unggah Buku
                            </button>
                        </div>
                    </div>
                </form>
        </div>
    </AuthenticatedLayout>
</template>
