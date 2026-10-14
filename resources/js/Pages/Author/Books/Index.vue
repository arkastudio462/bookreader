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
    draft: 'bg-soft text-ink',
    pending_review: 'bg-soft text-muted',
    published: 'bg-ink text-white',
    rejected: 'bg-soft text-muted',
    archived: 'bg-soft text-faint',
};
</script>

<template>
    <Head title="Kelola Buku - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Kelola Buku Saya</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <p class="lt text-muted">Total {{ books.total }} buku</p>
                    <Link :href="route('author.books.create')" class="btn-primary">
                        Unggah Buku Baru
                    </Link>
                </div>

                <div class="overflow-hidden rounded-md border border-hairline-soft bg-canvas">
                    <table class="min-w-full divide-y divide-hair">
                        <thead class="bg-soft">
                            <tr>
                                <th class="lk px-6 py-3 text-left text-xs uppercase tracking-wider text-muted">Judul</th>
                                <th class="lk px-6 py-3 text-left text-xs uppercase tracking-wider text-muted">Status</th>
                                <th class="lk px-6 py-3 text-left text-xs uppercase tracking-wider text-muted">Harga</th>
                                <th class="lk px-6 py-3 text-left text-xs uppercase tracking-wider text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hair bg-canvas">
                            <tr v-for="book in books.data" :key="book.id">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="lk text-ink">{{ book.title }}</div>
                                    <div class="text-sm text-muted">{{ book.author_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span :class="['lk inline-flex rounded-full px-2 text-xs leading-5', statusClasses[book.status]]">
                                        {{ statusLabels[book.status] || book.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-ink">
                                    Rp {{ Number(book.price).toLocaleString('id-ID') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link :href="route('author.books.show', book.id)" class="lk text-ink hover:text-ink-soft">Detail</Link>
                                    <span class="mx-2 text-faint">|</span>
                                    <Link v-if="book.status === 'draft' || book.status === 'rejected'" :href="route('author.books.edit', book.id)" class="lk text-ink hover:text-ink-soft">Edit</Link>
                                </td>
                            </tr>
                            <tr v-if="books.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-muted">Belum ada buku. Unggah buku pertama Anda.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="books.last_page > 1" class="flex items-center justify-between border-t border-hair px-6 py-4">
                        <div class="text-sm text-muted">Halaman {{ books.current_page }} dari {{ books.last_page }}</div>
                        <div class="flex gap-1">
                            <Link v-if="books.prev_page_url" :href="books.prev_page_url" class="rounded-full border border-hair px-3 py-1 text-sm text-ink hover:bg-soft">←</Link>
                            <template v-for="link in books.links.slice(1, -1)" :key="link.label">
                                <Link v-if="link.url" :href="link.url" :class="['rounded-full px-3 py-1 text-sm', link.active ? 'bg-ink text-white' : 'border border-hair text-ink hover:bg-soft']" v-html="link.label"></Link>
                            </template>
                            <Link v-if="books.next_page_url" :href="books.next_page_url" class="rounded-full border border-hair px-3 py-1 text-sm text-ink hover:bg-soft">→</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
