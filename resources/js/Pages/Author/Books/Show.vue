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
                <h2 class="h text-xl text-ink">Detail Buku</h2>
                <Link :href="route('author.books.index')" class="lk text-sm text-ink hover:text-ink-soft">← Kembali</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-md border border-hairline-soft bg-canvas">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="h text-2xl text-ink">{{ book.title }}</h3>
                                <p class="lt mt-1 text-muted">oleh {{ book.author_name }}</p>
                            </div>
                            <span class="lk rounded-full bg-soft px-3 py-1 text-sm text-ink">{{ statusLabels[book.status] || book.status }}</span>
                        </div>

                        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="lk text-sm text-muted">Harga</dt>
                                <dd class="mt-1 text-ink">Rp {{ Number(book.price).toLocaleString('id-ID') }}</dd>
                            </div>
                            <div>
                                <dt class="lk text-sm text-muted">Kategori</dt>
                                <dd class="mt-1 text-ink">{{ book.category?.name || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="lk text-sm text-muted">ISBN</dt>
                                <dd class="mt-1 text-ink">{{ book.isbn || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="lk text-sm text-muted">Bahasa</dt>
                                <dd class="mt-1 text-ink">{{ book.language === 'id' ? 'Indonesia' : 'English' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <dt class="lk text-sm text-muted">Deskripsi</dt>
                            <dd class="mt-1 whitespace-pre-line text-ink-soft">{{ book.description }}</dd>
                        </div>

                        <div v-if="book.status === 'rejected'" class="mt-6 rounded-md bg-soft p-4 text-sm text-ink">
                            Buku ditolak moderasi. Silakan perbaiki lalu ajukan ulang.
                        </div>

                        <div class="mt-8 flex gap-4">
                            <Link v-if="canEdit" :href="route('author.books.edit', book.id)" class="btn-primary">
                                Edit Buku
                            </Link>
                            <button v-if="canDelete" @click="destroy" class="btn-outline">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
