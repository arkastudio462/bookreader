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

const statusColors = {
    draft: 'bg-soft text-muted border-hair',
    pending_review: 'bg-amber-50 text-amber-900 border-amber-200',
    published: 'bg-emerald-50 text-emerald-900 border-emerald-200',
    rejected: 'bg-rose-50 text-rose-900 border-rose-200',
    archived: 'bg-soft text-faint border-hair',
};

const canEdit = computed(() => ['draft', 'rejected'].includes(props.book.status));
const canDelete = computed(() => props.book.status !== 'published');

const meta = computed(() => [
    { label: 'Harga', value: 'Rp ' + Number(props.book.price).toLocaleString('id-ID'), icon: 'payments' },
    { label: 'Kategori', value: props.book.category?.name || '-', icon: 'category' },
    { label: 'ISBN', value: props.book.isbn || '-', icon: 'tag' },
    { label: 'Bahasa', value: props.book.language === 'id' ? 'Indonesia' : 'English', icon: 'language' },
    { label: 'Halaman', value: props.book.pages || '-', icon: 'menu_book' },
    { label: 'Format', value: (props.book.type || 'epub').toUpperCase(), icon: 'description' },
]);

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
            <h2 class="h text-xl text-ink">Detail Buku</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <Link :href="route('author.books.index')" class="transition-colors hover:text-ink">Kelola Buku</Link>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="truncate font-semibold text-ink">{{ book.title }}</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-space-sm">
                        <h1 class="text-headline-sm tracking-tight text-ink">{{ book.title }}</h1>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-badge-label" :class="statusColors[book.status]">
                            {{ statusLabels[book.status] || book.status }}
                        </span>
                    </div>
                    <p class="text-body-md text-muted">oleh {{ book.author_name }}</p>
                </div>
                <Link :href="route('author.books.index')" class="btn-outline self-start gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Semua Buku
                </Link>
            </header>

            <div
                v-if="book.status === 'rejected'"
                class="flex items-start gap-space-sm rounded-[20px] border border-rose-200 bg-rose-50 p-space-md text-body-sm text-rose-900"
            >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                <span>Buku ditolak moderasi. Silakan perbaiki lalu ajukan ulang.</span>
            </div>

            <div class="grid grid-cols-1 gap-space-lg lg:grid-cols-12">
                <section class="rounded-[24px] border border-hair bg-canvas p-space-lg lg:col-span-7">
                    <div class="border-b border-hairline-soft pb-space-sm">
                        <h2 class="text-title-md text-ink">Detail Naskah</h2>
                    </div>

                    <dl class="mt-space-md grid grid-cols-1 gap-space-md sm:grid-cols-2">
                        <div v-for="item in meta" :key="item.label" class="rounded-2xl bg-soft p-space-md">
                            <dt class="flex items-center gap-1.5 text-caption text-muted">
                                <span class="material-symbols-outlined text-[16px]">{{ item.icon }}</span>
                                {{ item.label }}
                            </dt>
                            <dd class="mt-1 text-action text-ink">{{ item.value }}</dd>
                        </div>
                    </dl>

                    <div class="mt-space-lg border-t border-hairline-soft pt-space-md">
                        <h3 class="text-caption text-muted uppercase">Deskripsi</h3>
                        <p class="mt-space-sm whitespace-pre-line text-body-md text-ink-soft">{{ book.description || 'Belum ada deskripsi.' }}</p>
                    </div>
                </section>

                <aside class="space-y-space-md lg:col-span-5">
                    <div class="rounded-[24px] border border-hair bg-soft p-space-lg">
                        <h2 class="text-title-md text-ink">Tindakan</h2>
                        <div class="mt-space-md space-y-space-sm">
                            <Link v-if="canEdit" :href="route('author.books.edit', book.id)" class="btn-primary w-full gap-2">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit Buku
                            </Link>
                            <button v-if="canDelete" type="button" class="btn-outline w-full border-error text-error hover:bg-rose-50" @click="destroy">
                                Hapus Buku
                            </button>
                            <p class="text-caption text-muted">Buku yang sudah terbit tidak dapat dihapus langsung.</p>
                        </div>
                    </div>

                    <div class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                        <h2 class="text-title-md text-ink">Proses Moderasi</h2>
                        <ol class="mt-space-md space-y-space-md">
                            <li class="flex items-start gap-space-sm">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-ink text-white">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                </span>
                                <span>
                                    <span class="block text-body-sm text-ink">Naskah diunggah</span>
                                    <span class="block text-caption text-muted">Berkas EPUB/PDF &amp; sampul diterima</span>
                                </span>
                            </li>
                            <li class="flex items-start gap-space-sm">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full" :class="book.status === 'draft' ? 'bg-field text-muted' : 'bg-ink text-white'">
                                    <span class="material-symbols-outlined text-[14px]">{{ book.status === 'draft' ? 'pending' : 'check' }}</span>
                                </span>
                                <span>
                                    <span class="block text-body-sm text-ink">Review kurator</span>
                                    <span class="block text-caption text-muted">Pemeriksaan isi, hak cipta, dan ISBN</span>
                                </span>
                            </li>
                            <li class="flex items-start gap-space-sm">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full" :class="book.status === 'published' ? 'bg-ink text-white' : 'bg-field text-muted'">
                                    <span class="material-symbols-outlined text-[14px]">{{ book.status === 'published' ? 'check' : 'schedule' }}</span>
                                </span>
                                <span>
                                    <span class="block text-body-sm text-ink">Tayang di katalog</span>
                                    <span class="block text-caption text-muted">Royalti 70% aktif setelah terbit</span>
                                </span>
                            </li>
                        </ol>
                    </div>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
