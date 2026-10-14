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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Unggah Buku Baru</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4 rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Judul *</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">ISBN</label>
                                <input v-model="form.isbn" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Penulis *</label>
                                <input v-model="form.author_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori *</label>
                                <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kategori</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bahasa *</label>
                                <select v-model="form.language" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="id">Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga Jual (Rp) *</label>
                                <input v-model.number="form.price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span v-if="form.errors.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi *</label>
                            <textarea v-model="form.description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            <span v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</span>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg bg-white p-6 shadow">
                        <h3 class="text-lg font-medium text-gray-900">File & Cover</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cover Buku *</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <div v-if="coverPreview" class="h-24 w-16 overflow-hidden rounded">
                                    <img :src="coverPreview" alt="Cover Preview" class="h-full w-full object-cover">
                                </div>
                                <input @change="handleCoverChange" type="file" accept="image/*" class="block w-full text-sm text-gray-500">
                            </div>
                            <span v-if="form.errors.cover" class="mt-1 text-xs text-red-600">{{ form.errors.cover }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Buku (EPUB/PDF) *</label>
                            <div class="mt-1 flex items-center">
                                <span v-if="filePreview" class="mr-4 text-sm text-gray-700">{{ filePreview }}</span>
                                <input @change="handleFileChange" type="file" accept=".epub,.pdf" class="block w-full text-sm text-gray-500">
                            </div>
                            <span v-if="form.errors.file" class="mt-1 text-xs text-red-600">{{ form.errors.file }}</span>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex justify-end gap-4">
                            <Link :href="route('author.books.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
                                Batal
                            </Link>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Unggah Buku
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
