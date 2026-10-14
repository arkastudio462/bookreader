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
    active: 'bg-emerald-50 text-emerald-900 border-emerald-200',
    draft: 'bg-amber-50 text-amber-900 border-amber-200',
    ended: 'bg-soft text-muted border-hair',
};

const statusDot = {
    active: 'bg-emerald-600',
    draft: 'bg-amber-600',
    ended: 'bg-faint',
};

const statusLabel = { active: 'Berjalan', draft: 'Dijadwalkan', ended: 'Selesai' };
</script>

<template>
    <Head title="Flash Sale - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h text-xl text-ink">Kampanye Flash Sale</h2>
        </template>

        <div class="space-y-space-lg">
            <header class="flex flex-col justify-between gap-space-lg border-b border-hair pb-space-lg lg:flex-row lg:items-end">
                <div class="max-w-3xl space-y-space-xs">
                    <nav class="flex items-center gap-space-xs text-muted text-caption" aria-label="Breadcrumb">
                        <span>Admin</span>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="font-semibold text-ink">Flash Sale</span>
                    </nav>
                    <h1 class="text-headline-sm tracking-tight text-ink">Manajemen Flash Sale</h1>
                    <p class="text-body-md text-muted">Buat, jadwalkan, dan pantau kampanye potongan harga terbatas.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-soft px-3 py-1.5 text-badge-label text-ink lg:self-auto">
                    <span class="material-symbols-outlined text-[16px]">bolt</span>
                    Maks. diskon {{ maxDiscountPercent }}%
                </span>
            </header>

            <form class="rounded-[24px] border border-hair bg-canvas p-space-lg" @submit.prevent="submit">
                <div class="flex items-center justify-between border-b border-hairline-soft pb-space-sm">
                    <div class="flex items-center gap-space-sm">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-soft">
                            <span class="material-symbols-outlined text-[18px] text-ink">{{ isEditing ? 'edit' : 'add_circle' }}</span>
                        </span>
                        <div>
                            <h3 class="text-title-md text-ink">{{ isEditing ? 'Edit Kampanye' : 'Buat Kampanye Baru' }}</h3>
                            <p class="text-caption text-muted">Isi detail kampanye dan pilih buku yang ikut serta.</p>
                        </div>
                    </div>
                    <span v-if="isEditing" class="rounded-full bg-field px-2.5 py-1 text-badge-label text-ink">Mode edit</span>
                </div>

                <div class="mt-space-md grid grid-cols-1 gap-space-md sm:grid-cols-2">
                    <div>
                        <label class="block text-body-sm text-ink">Nama Kampanye *</label>
                        <input v-model="form.name" type="text" class="field mt-1 block w-full">
                        <span v-if="form.errors.name" class="mt-1 text-caption text-error">{{ form.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-body-sm text-ink">Teks Banner</label>
                        <input v-model="form.banner_text" type="text" class="field mt-1 block w-full">
                    </div>
                    <div>
                        <label class="block text-body-sm text-ink">Mulai *</label>
                        <input v-model="form.starts_at" type="datetime-local" class="field mt-1 block w-full">
                        <span v-if="form.errors.starts_at" class="mt-1 text-caption text-error">{{ form.errors.starts_at }}</span>
                    </div>
                    <div>
                        <label class="block text-body-sm text-ink">Berakhir *</label>
                        <input v-model="form.ends_at" type="datetime-local" class="field mt-1 block w-full">
                        <span v-if="form.errors.ends_at" class="mt-1 text-caption text-error">{{ form.errors.ends_at }}</span>
                    </div>
                </div>

                <div class="mt-space-lg border-t border-hairline-soft pt-space-md">
                    <div class="flex items-center justify-between">
                        <label class="block text-body-sm text-ink">Buku dalam Kampanye</label>
                        <button type="button" class="inline-flex items-center gap-1 text-caption text-ink transition-colors hover:text-accent" @click="addRow">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Tambah Buku
                        </button>
                    </div>
                    <p class="mt-1 text-caption text-muted">Diskon maksimal {{ maxDiscountPercent }}% dari harga buku.</p>

                    <div v-for="(item, index) in form.items" :key="index" class="mt-space-md grid grid-cols-1 gap-space-sm rounded-2xl bg-soft p-space-sm sm:grid-cols-12">
                        <select v-model="item.book_id" class="field sm:col-span-5">
                            <option value="">Pilih buku...</option>
                            <option v-for="b in books" :key="b.id" :value="b.id">
                                {{ b.title }} — Rp {{ Number(b.price).toLocaleString('id-ID') }}
                            </option>
                        </select>
                        <input v-model.number="item.sale_price" type="number" min="0" placeholder="Harga sale" class="field sm:col-span-3">
                        <input v-model.number="item.stock_limit" type="number" min="1" placeholder="Batas terjual" class="field sm:col-span-3">
                        <button type="button" class="grid h-11 place-items-center rounded-full border border-hair bg-canvas text-ink transition-colors hover:bg-soft sm:col-span-1" aria-label="Hapus baris" @click="removeRow(index)">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                        <span v-if="item.book_id && item.sale_price" class="text-caption text-muted sm:col-span-12">
                            Harga normal Rp {{ bookPrice(item.book_id).toLocaleString('id-ID') }} → potongan
                            {{ Math.round((1 - Number(item.sale_price) / bookPrice(item.book_id)) * 100) }}%
                        </span>
                        <span v-if="form.errors[`items.${index}.sale_price`]" class="text-caption text-error sm:col-span-12">
                            {{ form.errors[`items.${index}.sale_price`] }}
                        </span>
                        <span v-if="form.errors[`items.${index}.book_id`]" class="text-caption text-error sm:col-span-12">
                            {{ form.errors[`items.${index}.book_id`] }}
                        </span>
                    </div>
                </div>

                <div class="mt-space-lg flex justify-end gap-space-sm border-t border-hairline-soft pt-space-md">
                    <button v-if="isEditing" type="button" class="btn-outline text-sm" @click="resetForm">Batal</button>
                    <button type="submit" class="btn-primary disabled:opacity-50" :disabled="form.processing">
                        {{ isEditing ? 'Simpan Perubahan' : 'Buat Kampanye' }}
                    </button>
                </div>
            </form>

            <section class="space-y-space-md">
                <div class="flex items-end justify-between border-b border-hair pb-space-sm">
                    <h3 class="text-title-lg text-ink">Daftar Kampanye</h3>
                    <span class="text-body-sm text-muted">{{ campaigns.length }} kampanye</span>
                </div>

                <div v-if="campaigns.length === 0" class="rounded-[24px] border border-hair bg-canvas px-space-lg py-space-xl text-center">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-soft">
                        <span class="material-symbols-outlined text-[28px] text-muted">bolt</span>
                    </div>
                    <p class="mt-space-md text-title-md text-ink">Belum ada kampanye</p>
                    <p class="mt-space-xs text-body-md text-muted">Buat kampanye flash sale pertama Anda di atas.</p>
                </div>

                <article v-for="campaign in campaigns" :key="campaign.id" class="rounded-[24px] border border-hair bg-canvas p-space-lg">
                    <div class="flex flex-wrap items-start justify-between gap-space-md">
                        <div class="space-y-space-xs">
                            <div class="flex flex-wrap items-center gap-space-sm">
                                <h4 class="text-title-md text-ink">{{ campaign.name }}</h4>
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-badge-label" :class="statusClass[campaign.status]">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="statusDot[campaign.status]"></span>
                                    {{ statusLabel[campaign.status] || campaign.status }}
                                </span>
                            </div>
                            <p v-if="campaign.banner_text" class="text-body-sm text-muted">{{ campaign.banner_text }}</p>
                            <p class="flex items-center gap-1 text-caption text-muted">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                {{ new Date(campaign.starts_at).toLocaleString('id-ID') }} — {{ new Date(campaign.ends_at).toLocaleString('id-ID') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-space-xs">
                            <button v-if="campaign.status !== 'active'" type="button" class="rounded-full bg-ink px-space-md py-1.5 text-caption text-white transition-opacity hover:opacity-90" @click="activate(campaign)">Aktifkan</button>
                            <button v-if="campaign.status === 'active'" type="button" class="rounded-full bg-field px-space-md py-1.5 text-caption text-ink transition-colors hover:bg-hair" @click="end(campaign)">Akhiri</button>
                            <button type="button" class="rounded-full border border-hair bg-canvas px-space-md py-1.5 text-caption text-ink transition-colors hover:bg-soft" @click="edit(campaign)">Edit</button>
                            <button type="button" class="rounded-full border border-error bg-canvas px-space-md py-1.5 text-caption text-error transition-colors hover:bg-rose-50" @click="destroy(campaign)">Hapus</button>
                        </div>
                    </div>

                    <div class="mt-space-md overflow-x-auto rounded-2xl border border-hairline-soft">
                        <table class="w-full border-collapse text-left text-body-sm">
                            <thead>
                                <tr class="border-b border-hairline-soft bg-soft text-muted text-caption uppercase">
                                    <th class="px-space-md py-space-sm font-semibold">Buku</th>
                                    <th class="px-space-md py-space-sm font-semibold">Harga Normal</th>
                                    <th class="px-space-md py-space-sm font-semibold">Harga Sale</th>
                                    <th class="px-space-md py-space-sm font-semibold">Terjual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline-soft text-ink">
                                <tr v-for="item in campaign.items" :key="item.id">
                                    <td class="px-space-md py-space-sm">{{ item.book ? item.book.title : '-' }}</td>
                                    <td class="px-space-md py-space-sm text-muted">Rp {{ Number(item.book?.price ?? 0).toLocaleString('id-ID') }}</td>
                                    <td class="px-space-md py-space-sm font-semibold">Rp {{ Number(item.sale_price).toLocaleString('id-ID') }}</td>
                                    <td class="px-space-md py-space-sm">
                                        {{ item.sold_count }}
                                        <span v-if="item.stock_limit" class="text-muted">/ {{ item.stock_limit }} ({{ item.sold_percent }}%)</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
