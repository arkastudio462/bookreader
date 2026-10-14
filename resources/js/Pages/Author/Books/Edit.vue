<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    book: Object,
    categories: Array,
    genres: Array,
});

const form = useForm({
    title: props.book.title,
    description: props.book.description,
    author_name: props.book.author_name,
    isbn: props.book.isbn || '',
    category_id: props.book.category_id,
    genre_id: props.book.genre_id || '',
    language: props.book.language,
    price: props.book.price,
    sample_price: props.book.sample_price || '',
    pages: props.book.pages || '',
});

// Ubah ISO 8601 menjadi format input datetime-local.
function toLocal(value) {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

const discountForm = useForm({
    discount_price: props.book.discount_price ?? '',
    discount_starts_at: toLocal(props.book.discount_starts_at),
    discount_ends_at: toLocal(props.book.discount_ends_at),
});

function submit() {
    form.patch(route('author.books.update', props.book.id));
}

function submitDiscount() {
    discountForm.patch(route('author.books.discount', props.book.id), { preserveScroll: true });
}

function clearDiscount() {
    discountForm.discount_price = '';
    discountForm.discount_starts_at = '';
    discountForm.discount_ends_at = '';
    submitDiscount();
}
</script>

<template>
    <Head :title="'Edit ' + book.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Edit Buku</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4 card">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="lk block text-sm text-ink-soft">Judul *</label>
                                <input v-model="form.title" type="text" class="field mt-1 block w-full">
                                <span v-if="form.errors.title" class="mt-1 text-xs text-ink">{{ form.errors.title }}</span>
                            </div>
                            <div>
                                <label class="lk block text-sm text-ink-soft">ISBN</label>
                                <input v-model="form.isbn" type="text" class="field mt-1 block w-full">
                            </div>
                            <div>
                                <label class="lk block text-sm text-ink-soft">Nama Penulis *</label>
                                <input v-model="form.author_name" type="text" class="field mt-1 block w-full">
                            </div>
                            <div>
                                <label class="lk block text-sm text-ink-soft">Kategori *</label>
                                <select v-model="form.category_id" class="field mt-1 block w-full">
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="lk block text-sm text-ink-soft">Bahasa *</label>
                                <select v-model="form.language" class="field mt-1 block w-full">
                                    <option value="id">Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                            <div>
                                <label class="lk block text-sm text-ink-soft">Harga Jual (Rp) *</label>
                                <input v-model.number="form.price" type="number" class="field mt-1 block w-full">
                            </div>
                        </div>
                        <div>
                            <label class="lk block text-sm text-ink-soft">Deskripsi *</label>
                            <textarea v-model="form.description" rows="4" class="field mt-1 block w-full"></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex justify-end gap-4">
                            <Link :href="route('author.books.show', book.id)" class="btn-outline">Batal</Link>
                            <button type="submit" :disabled="form.processing" class="btn-primary disabled:opacity-50">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Diskon terjadwal per buku (FR-4.4.4 / FR-4.7.1) -->
                <form @submit.prevent="submitDiscount" class="space-y-4 card">
                    <div>
                        <h3 class="h text-lg text-ink">Diskon Terjadwal</h3>
                        <p class="lt mt-1 text-sm text-muted">
                            Harga coret otomatis tampil saat jadwal aktif dan kembali normal setelah berakhir.
                            Harga jual saat ini: <strong>Rp {{ Number(book.price).toLocaleString('id-ID') }}</strong>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="lk block text-sm text-ink-soft">Harga Diskon (Rp)</label>
                            <input v-model.number="discountForm.discount_price" type="number" min="0"
                                class="field mt-1 block w-full">
                            <span v-if="discountForm.errors.discount_price" class="mt-1 text-xs text-ink">{{ discountForm.errors.discount_price }}</span>
                        </div>
                        <div>
                            <label class="lk block text-sm text-ink-soft">Mulai</label>
                            <input v-model="discountForm.discount_starts_at" type="datetime-local"
                                class="field mt-1 block w-full">
                            <span v-if="discountForm.errors.discount_starts_at" class="mt-1 text-xs text-ink">{{ discountForm.errors.discount_starts_at }}</span>
                        </div>
                        <div>
                            <label class="lk block text-sm text-ink-soft">Berakhir</label>
                            <input v-model="discountForm.discount_ends_at" type="datetime-local"
                                class="field mt-1 block w-full">
                            <span v-if="discountForm.errors.discount_ends_at" class="mt-1 text-xs text-ink">{{ discountForm.errors.discount_ends_at }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="clearDiscount" :disabled="discountForm.processing"
                            class="btn-outline disabled:opacity-50">
                            Hapus Diskon
                        </button>
                        <button type="submit" :disabled="discountForm.processing"
                            class="btn-primary disabled:opacity-50">
                            Simpan Diskon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
