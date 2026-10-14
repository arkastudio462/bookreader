<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ books: Object });

const statusLabels = {
    draft: 'Draft',
    pending_review: 'Menunggu Review',
    published: 'Terbit',
    rejected: 'Ditolak',
    archived: 'Diarsipkan',
};

const statusClasses = {
    draft: 'bg-gray-100 text-gray-800',
    pending_review: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    archived: 'bg-gray-100 text-gray-500',
};
</script>

<template>
    <Head title="Kelola Buku - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Buku Saya</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600">Total {{ books.total }} buku</p>
                    <Link :href="route('author.books.create')" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        Unggah Buku Baru
                    </Link>
                </div>

                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="book in books.data" :key="book.id">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ book.title }}</div>
                                    <div class="text-sm text-gray-500">{{ book.author_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="['inline-flex rounded-full px-2 text-xs font-semibold leading-5', statusClasses[book.status]]">
                                        {{ statusLabels[book.status] || book.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    Rp {{ Number(book.price).toLocaleString('id-ID') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <Link :href="route('author.books.show', book.id)" class="text-blue-600 hover:text-blue-900">Detail</Link>
                                    <span class="mx-2">|</span>
                                    <Link v-if="book.status === 'draft' || book.status === 'rejected'" :href="route('author.books.edit', book.id)" class="text-green-600 hover:text-green-900">Edit</Link>
                                </td>
                            </tr>
                            <tr v-if="books.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada buku. Unggah buku pertama Anda.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="books.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
                        <div class="text-sm text-gray-700">Halaman {{ books.current_page }} dari {{ books.last_page }}</div>
                        <div class="flex gap-1">
                            <Link v-if="books.prev_page_url" :href="books.prev_page_url" class="rounded-md border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">←</Link>
                            <template v-for="link in books.links.slice(1, -1)" :key="link.label">
                                <Link v-if="link.url" :href="link.url" :class="['rounded-md px-3 py-1 text-sm', link.active ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-50']" v-html="link.label"></Link>
                            </template>
                            <Link v-if="books.next_page_url" :href="books.next_page_url" class="rounded-md border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">→</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
