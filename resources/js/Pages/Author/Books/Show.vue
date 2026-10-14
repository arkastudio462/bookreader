<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({ book: Object });

const statusLabels = {
    draft: 'Draft',
    pending_review: 'Menunggu Review',
    published: 'Terbit',
    rejected: 'Ditolak',
    archived: 'Diarsipkan',
};

const canEdit = computed(() => ['draft', 'rejected'].includes(props.book.status));
const canDelete = computed(() => props.book.status !== 'published');

function destroy() {
    if (confirm('Yakin hapus buku ini?')) {
        router.delete(route('author.books.destroy', props.book.id));
    }
}
</script>

<template>
    <Head :title="book.title + ' - Kelola Buku'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Buku</h2>
                <Link :href="route('author.books.index')" class="text-sm text-blue-600 hover:underline">← Kembali</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ book.title }}</h3>
                                <p class="mt-1 text-gray-600">oleh {{ book.author_name }}</p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold">{{ statusLabels[book.status] || book.status }}</span>
                        </div>

                        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Harga</dt>
                                <dd class="mt-1 text-gray-900">Rp {{ Number(book.price).toLocaleString('id-ID') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="mt-1 text-gray-900">{{ book.category?.name || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                <dd class="mt-1 text-gray-900">{{ book.isbn || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bahasa</dt>
                                <dd class="mt-1 text-gray-900">{{ book.language === 'id' ? 'Indonesia' : 'English' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                            <dd class="mt-1 whitespace-pre-line text-gray-700">{{ book.description }}</dd>
                        </div>

                        <div v-if="book.status === 'rejected'" class="mt-6 rounded-md bg-red-50 p-4 text-sm text-red-800">
                            Buku ditolak moderasi. Silakan perbaiki lalu ajukan ulang.
                        </div>

                        <div class="mt-8 flex gap-4">
                            <Link v-if="canEdit" :href="route('author.books.edit', book.id)" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Edit Buku
                            </Link>
                            <button v-if="canDelete" @click="destroy" class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
