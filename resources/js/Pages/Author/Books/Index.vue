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

const statusColors = {
    draft: 'bg-soft text-muted border-hair',
    pending_review: 'bg-amber-50 text-amber-900 border-amber-200',
    published: 'bg-emerald-50 text-emerald-900 border-emerald-200',
    rejected: 'bg-rose-50 text-rose-900 border-rose-200',
    archived: 'bg-soft text-faint border-hair',
};

const statusDots = {
    draft: 'bg-faint',
    pending_review: 'bg-amber-600',
    published: 'bg-emerald-600',
    rejected: 'bg-error',
    archived: 'bg-faint',
};
</script>

<template>
    <Head title="Kelola Buku - PerpusKita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Kelola Buku Saya</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Studio</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Kelola Buku</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Kelola Buku</h1>
                    <p class="text-body-md text-muted">Unggah naskah, pantau status moderasi, dan perbarui detail terbitan Anda.</p>
                </div>
                <Link :href="route('author.books.create')" class="btn-primary self-start gap-2">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    Unggah Buku Baru
                </Link>
            </header>

            <section class="overflow-hidden rounded-[24px] border border-hair bg-canvas">
                <div class="flex items-center justify-between border-b border-hairline-soft px-space-lg py-space-sm">
                    <span class="text-body-sm text-muted">Total <span class="font-semibold text-ink">{{ books.total }}</span> buku</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-soft px-2.5 py-1 text-badge-label text-ink">
                        <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                        Katalog Anda
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-hairline-soft text-muted text-caption uppercase">
                                <th class="px-space-lg py-space-sm font-semibold">Judul</th>
                                <th class="px-space-md py-space-sm font-semibold">Status</th>
                                <th class="px-space-md py-space-sm font-semibold">Harga</th>
                                <th class="px-space-md py-space-sm text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft text-body-sm text-ink">
                            <tr v-for="book in books.data" :key="book.id" class="transition-colors hover:bg-soft/60">
                                <td class="px-space-lg py-space-md">
                                    <Link :href="route('author.books.show', book.id)" class="text-action text-ink hover:underline">{{ book.title }}</Link>
                                    <div class="text-caption text-muted">{{ book.author_name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-space-md py-space-md">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-badge-label" :class="statusColors[book.status]">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="statusDots[book.status]"></span>
                                        {{ statusLabels[book.status] || book.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-space-md py-space-md text-body-sm text-ink">
                                    Rp {{ Number(book.price).toLocaleString('id-ID') }}
                                </td>
                                <td class="whitespace-nowrap px-space-md py-space-md text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <Link :href="route('author.books.show', book.id)" class="rounded-full bg-field px-3 py-1.5 text-caption text-ink transition-colors hover:bg-hair">Detail</Link>
                                        <Link
                                            v-if="book.status === 'draft' || book.status === 'rejected'"
                                            :href="route('author.books.edit', book.id)"
                                            class="rounded-full bg-ink px-3 py-1.5 text-caption text-white transition-opacity hover:opacity-90"
                                        >
                                            Edit
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="books.data.length === 0">
                                <td colspan="4" class="px-space-lg py-space-xl text-center">
                                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                                        <span class="material-symbols-outlined text-[28px] text-muted">upload_file</span>
                                    </div>
                                    <p class="mt-space-md text-title-md text-ink">Belum ada buku</p>
                                    <p class="mt-space-xs text-body-sm text-muted">Unggah buku pertama Anda untuk mulai menerbitkan.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="books.last_page > 1" class="flex items-center justify-between border-t border-hairline-soft px-space-lg py-space-md">
                    <span class="text-body-sm text-muted">Halaman {{ books.current_page }} dari {{ books.last_page }}</span>
                    <div class="flex gap-space-xs">
                        <Link v-if="books.prev_page_url" :href="books.prev_page_url" class="grid h-8 w-8 place-items-center rounded-full bg-field text-ink transition-colors hover:bg-hair" aria-label="Halaman sebelumnya">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </Link>
                        <template v-for="link in books.links.slice(1, -1)" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="grid h-8 w-8 place-items-center rounded-full text-caption transition-colors"
                                :class="link.active ? 'bg-ink text-white' : 'bg-field text-ink hover:bg-hair'"
                                v-html="link.label"
                            ></Link>
                        </template>
                        <Link v-if="books.next_page_url" :href="books.next_page_url" class="grid h-8 w-8 place-items-center rounded-full bg-field text-ink transition-colors hover:bg-hair" aria-label="Halaman berikutnya">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
