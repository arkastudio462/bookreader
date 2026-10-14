<script setup>
import { computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    campaigns: Array,
    books: Array,
    maxDiscountPercent: Number,
});

const pad = (n) => String(n).padStart(2, '0');
function localValue(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

const now = new Date();
const later = new Date(now.getTime() + 24 * 60 * 60 * 1000);

const form = useForm({
    id: null,
    name: '',
    banner_text: '',
    starts_at: localValue(now),
    ends_at: localValue(later),
    items: [{ book_id: '', sale_price: '', stock_limit: '' }],
});

const isEditing = computed(() => form.id !== null);

function addRow() {
    form.items.push({ book_id: '', sale_price: '', stock_limit: '' });
}

function removeRow(index) {
    if (form.items.length > 1) form.items.splice(index, 1);
}

function bookPrice(bookId) {
    const book = props.books.find((b) => b.id === Number(bookId));
    return book ? Number(book.price) : 0;
}

function resetForm() {
    form.reset();
    form.id = null;
    form.items = [{ book_id: '', sale_price: '', stock_limit: '' }];
    form.starts_at = localValue(new Date());
    form.ends_at = localValue(new Date(Date.now() + 24 * 60 * 60 * 1000));
}

function submit() {
    if (isEditing.value) {
        form.patch(route('admin.flash-sale.update', form.id), { onSuccess: resetForm, preserveScroll: true });
    } else {
        form.post(route('admin.flash-sale.store'), { onSuccess: resetForm, preserveScroll: true });
    }
}

function edit(campaign) {
    form.id = campaign.id;
    form.name = campaign.name;
    form.banner_text = campaign.banner_text || '';
    form.starts_at = localValue(new Date(campaign.starts_at));
    form.ends_at = localValue(new Date(campaign.ends_at));
    form.items = campaign.items.map((i) => ({
        book_id: i.book_id,
        sale_price: Number(i.sale_price),
        stock_limit: i.stock_limit ?? '',
    }));
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function activate(campaign) {
    router.post(route('admin.flash-sale.activate', campaign.id), {}, { preserveScroll: true });
}

function end(campaign) {
    router.post(route('admin.flash-sale.end', campaign.id), {}, { preserveScroll: true });
}

function destroy(campaign) {
    if (confirm('Hapus kampanye ini?')) {
        router.delete(route('admin.flash-sale.destroy', campaign.id), { preserveScroll: true });
    }
}

const statusClass = {
    active: 'bg-green-100 text-green-700',
    draft: 'bg-yellow-100 text-yellow-700',
    ended: 'bg-gray-200 text-gray-600',
};
const statusLabel = { active: 'Berjalan', draft: 'Dijadwalkan', ended: 'Selesai' };
</script>

<template>
    <Head title="Flash Sale - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Kampanye Flash Sale</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4 rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ isEditing ? 'Edit Kampanye' : 'Buat Kampanye Baru' }}
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Kampanye *</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <span v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teks Banner</label>
                            <input v-model="form.banner_text" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mulai *</label>
                            <input v-model="form.starts_at" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <span v-if="form.errors.starts_at" class="mt-1 text-xs text-red-600">{{ form.errors.starts_at }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Berakhir *</label>
                            <input v-model="form.ends_at" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <span v-if="form.errors.ends_at" class="mt-1 text-xs text-red-600">{{ form.errors.ends_at }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-700">Buku dalam Kampanye</label>
                            <button type="button" @click="addRow" class="text-sm text-blue-600 hover:underline">+ Tambah Buku</button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Diskon maksimal {{ maxDiscountPercent }}% dari harga buku.</p>

                        <div v-for="(item, index) in form.items" :key="index" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-12">
                            <select v-model="item.book_id" class="sm:col-span-5 rounded-md border-gray-300 shadow-sm">
                                <option value="">Pilih buku...</option>
                                <option v-for="b in books" :key="b.id" :value="b.id">
                                    {{ b.title }} — Rp {{ Number(b.price).toLocaleString('id-ID') }}
                                </option>
                            </select>
                            <input v-model.number="item.sale_price" type="number" min="0" placeholder="Harga sale"
                                class="sm:col-span-3 rounded-md border-gray-300 shadow-sm">
                            <input v-model.number="item.stock_limit" type="number" min="1" placeholder="Batas terjual (opsional)"
                                class="sm:col-span-3 rounded-md border-gray-300 shadow-sm">
                            <button type="button" @click="removeRow(index)" class="sm:col-span-1 rounded-md border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                ✕
                            </button>
                            <span v-if="item.book_id && item.sale_price"
                                class="sm:col-span-12 text-xs text-gray-500">
                                Harga normal Rp {{ bookPrice(item.book_id).toLocaleString('id-ID') }} →
                                potongan {{ Math.round((1 - Number(item.sale_price) / bookPrice(item.book_id)) * 100) }}%
                            </span>
                            <span v-if="form.errors[`items.${index}.sale_price`]" class="sm:col-span-12 text-xs text-red-600">
                                {{ form.errors[`items.${index}.sale_price`] }}
                            </span>
                            <span v-if="form.errors[`items.${index}.book_id`]" class="sm:col-span-12 text-xs text-red-600">
                                {{ form.errors[`items.${index}.book_id`] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button v-if="isEditing" type="button" @click="resetForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                            {{ isEditing ? 'Simpan Perubahan' : 'Buat Kampanye' }}
                        </button>
                    </div>
                </form>

                <!-- Daftar Kampanye -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Kampanye</h3>

                    <div v-if="campaigns.length === 0" class="rounded-lg bg-white p-8 text-center text-gray-600 shadow">
                        Belum ada kampanye flash sale.
                    </div>

                    <div v-for="campaign in campaigns" :key="campaign.id" class="rounded-lg bg-white p-6 shadow">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ campaign.name }}</h4>
                                    <span class="rounded px-2 py-0.5 text-xs font-semibold" :class="statusClass[campaign.status]">
                                        {{ statusLabel[campaign.status] || campaign.status }}
                                    </span>
                                </div>
                                <p v-if="campaign.banner_text" class="mt-1 text-sm text-gray-600">{{ campaign.banner_text }}</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ new Date(campaign.starts_at).toLocaleString('id-ID') }}
                                    — {{ new Date(campaign.ends_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button v-if="campaign.status !== 'active'" @click="activate(campaign)"
                                    class="rounded-md bg-green-600 px-3 py-1.5 text-sm text-white hover:bg-green-700">Aktifkan</button>
                                <button v-if="campaign.status === 'active'" @click="end(campaign)"
                                    class="rounded-md bg-gray-600 px-3 py-1.5 text-sm text-white hover:bg-gray-700">Akhiri</button>
                                <button @click="edit(campaign)"
                                    class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Edit</button>
                                <button @click="destroy(campaign)"
                                    class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                            </div>
                        </div>

                        <table class="mt-4 w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-gray-500">
                                    <th class="py-2">Buku</th>
                                    <th class="py-2">Harga Normal</th>
                                    <th class="py-2">Harga Sale</th>
                                    <th class="py-2">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in campaign.items" :key="item.id" class="border-b last:border-0">
                                    <td class="py-2">{{ item.book ? item.book.title : '-' }}</td>
                                    <td class="py-2 text-gray-500">Rp {{ Number(item.book?.price ?? 0).toLocaleString('id-ID') }}</td>
                                    <td class="py-2 font-semibold text-blue-600">Rp {{ Number(item.sale_price).toLocaleString('id-ID') }}</td>
                                    <td class="py-2">
                                        {{ item.sold_count }}
                                        <span v-if="item.stock_limit" class="text-gray-500">/ {{ item.stock_limit }} ({{ item.sold_percent }}%)</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
