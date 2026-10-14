<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';

const props = defineProps({
    book: Object,
    mode: String,
    progress: Object,
    bookmarks: Array,
    highlights: Array,
    preferences: Object,
});

const isSample = computed(() => props.mode === 'sample');
const axios = window.axios;

/* ---------------- Preferences ---------------- */
const prefs = reactive({ ...props.preferences });
const isDark = computed(() => prefs.theme === 'dark');
const panelClass = computed(() => ({
    light: 'border-hair bg-canvas text-ink',
    sepia: 'border-[#ded1b7] bg-[#faf4e7] text-[#3b2f2f]',
    dark: 'border-white/15 bg-ink-soft text-white',
}[prefs.theme]));
const pillHover = computed(() => (isDark.value ? 'hover:bg-white/10' : 'hover:bg-black/5'));
const pillBg = computed(() => (isDark.value ? 'bg-white/10' : 'bg-black/10'));
const themeClasses = {
    light: 'bg-canvas text-ink',
    sepia: 'bg-[#f4ecd8] text-[#3b2f2f]',
    dark: 'bg-ink text-soft',
};

let prefTimer = null;
function savePrefs() {
    clearTimeout(prefTimer);
    prefTimer = setTimeout(() => {
        axios.patch(route('reader.preferences'), { ...prefs }).catch(() => {});
    }, 500);
}

/* ---------------- Shared state ---------------- */
const loading = ref(true);
const errorMsg = ref('');
const percent = ref(props.progress?.percent ?? 0);
const location = ref(props.progress?.location ?? null);
const showSettings = ref(false);
const showToc = ref(false);
const showBookmarks = ref(false);
const toc = ref([]);
const bookmarkList = ref([...props.bookmarks]);
const highlightList = ref([...props.highlights]);
const savedIndicator = ref(false);
const sampleLimitPct = 10;

let saveTimer = null;
function scheduleSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(saveProgress, 1500);
}
async function saveProgress() {
    try {
        await axios.post(route('reader.progress', props.book.id), {
            location: location.value,
            percent: Math.round(percent.value),
        });
        savedIndicator.value = true;
        setTimeout(() => (savedIndicator.value = false), 1500);
    } catch (e) { /* silent */ }
}

/* ---------------- EPUB ---------------- */
let epubBook = null;
let rendition = null;
const viewer = ref(null);
const pendingHighlight = ref(null);
const showHighlightPrompt = ref(false);
const epubRenditionReady = ref(false);

async function initEpub() {
    const ePub = (await import('epubjs')).default;
    epubBook = ePub(props.book.file_url);
    rendition = epubBook.renderTo(viewer.value, {
        width: '100%',
        height: '100%',
        flow: 'paginated',
        spread: 'auto',
    });

    const t = {
        light: { body: { background: '#ffffff', color: '#111827' } },
        sepia: { body: { background: '#f4ecd8', color: '#3b2f2f' } },
        dark: { body: { background: '#111827', color: '#e5e7eb' } },
    };
    Object.entries(t).forEach(([k, v]) => rendition.themes.register(k, v));
    applyEpubPrefs();

    epubBook.loaded.navigation.then((nav) => {
        toc.value = nav.toc.map((i) => ({ label: i.label.trim(), href: i.href }));
    });

    await rendition.display(props.progress?.location || undefined);

    epubBook.ready.then(() => epubBook.locations.generate(1200)).catch(() => {});
    epubBook.ready.then(() => {
        // restore highlights
        highlightList.value.forEach((h) => {
            try { rendition.annotations.highlight(h.location, {}, () => {}, 'hl', { fill: colorHex(h.color) }); } catch (e) {}
        });
    });

    rendition.on('relocated', (loc) => {
        const start = loc.start;
        location.value = start.cfi;
        if (epubBook.locations && epubBook.locations.length()) {
            const p = epubBook.locations.percentageFromCfi(start.cfi);
            percent.value = Math.min(100, Math.round((p || 0) * 100));
        }
        scheduleSave();
    });

    rendition.on('selected', (cfiRange, contents) => {
        const text = contents.window.getSelection().toString().trim();
        if (text) {
            pendingHighlight.value = { location: cfiRange, text };
            showHighlightPrompt.value = true;
        }
    });

    epubRenditionReady.value = true;
    loading.value = false;
}

function applyEpubPrefs() {
    if (!rendition) return;
    rendition.themes.select(prefs.theme);
    rendition.themes.fontSize(prefs.font_size + 'px');
    rendition.themes.font(prefs.font_family === 'serif' ? 'Georgia, serif' : 'system-ui, sans-serif');
    rendition.themes.override('line-height', String(prefs.line_height));
    const pad = { narrow: '18%', normal: '9%', wide: '3%' }[prefs.margin] || '9%';
    rendition.themes.override('padding-left', pad);
    rendition.themes.override('padding-right', pad);
}

/* ---------------- PDF ---------------- */
let pdfDoc = null;
const pdfPage = ref(1);
const pdfTotal = ref(0);
const canvas = ref(null);
let pdfRendering = false;
let pdfScale = 1.2;

async function initPdf() {
    const pdfjsLib = await import('pdfjs-dist');
    const workerUrl = (await import('pdfjs-dist/build/pdf.worker.min.mjs?url')).default;
    pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

    pdfDoc = await pdfjsLib.getDocument(props.book.file_url).promise;
    pdfTotal.value = pdfDoc.numPages;
    const startPage = props.progress?.location ? parseInt(props.progress.location, 10) : 1;
    await renderPdfPage(startPage || 1);
    loading.value = false;
}

async function renderPdfPage(num) {
    if (!pdfDoc || pdfRendering) return;
    pdfRendering = true;
    try {
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: pdfScale });
        const c = canvas.value;
        const ctx = c.getContext('2d');
        c.width = viewport.width;
        c.height = viewport.height;
        await page.render({ canvasContext: ctx, viewport }).promise;
        pdfPage.value = num;
        location.value = String(num);
        percent.value = Math.min(100, Math.round((num / pdfTotal.value) * 100));
        scheduleSave();
    } finally {
        pdfRendering = false;
    }
}

function pdfNext() {
    const max = isSample.value ? Math.max(1, Math.ceil(pdfTotal.value * sampleLimitPct / 100)) : pdfTotal.value;
    if (pdfPage.value < max) renderPdfPage(pdfPage.value + 1);
}
function pdfPrev() {
    if (pdfPage.value > 1) renderPdfPage(pdfPage.value - 1);
}

/* ---------------- Bookmarks ---------------- */
function currentLocation() {
    return location.value;
}
async function addBookmark() {
    if (!location.value) return;
    const label = window.prompt('Label bookmark (opsional):') || null;
    try {
        const res = await axios.post(route('reader.bookmarks.store', props.book.id), {
            location: location.value,
            label,
        });
        bookmarkList.value.unshift(res.data.bookmark);
    } catch (e) { /* silent */ }
}
async function removeBookmark(id) {
    try {
        await axios.delete(route('reader.bookmarks.destroy', { book: props.book.id, bookmark: id }));
        bookmarkList.value = bookmarkList.value.filter((b) => b.id !== id);
    } catch (e) { /* silent */ }
}
function goToBookmark(b) {
    goTo(b.location);
    showBookmarks.value = false;
}

/* ---------------- Highlights ---------------- */
function colorHex(c) {
    return { yellow: '#fde047', green: '#86efac', blue: '#93c5fd', pink: '#f9a8d4' }[c] || '#fde047';
}
async function confirmHighlight() {
    if (!pendingHighlight.value) return;
    const color = prefs.highlight_color || 'yellow';
    try {
        const res = await axios.post(route('reader.highlights.store', props.book.id), {
            location: pendingHighlight.value.location,
            text: pendingHighlight.value.text,
            color,
        });
        highlightList.value.unshift(res.data.highlight);
        if (rendition) {
            try {
                rendition.annotations.highlight(
                    pendingHighlight.value.location, {}, () => {}, 'hl', { fill: colorHex(color) }
                );
            } catch (e) {}
        }
    } catch (e) { /* silent */ }
    pendingHighlight.value = null;
    showHighlightPrompt.value = false;
}
function cancelHighlight() {
    pendingHighlight.value = null;
    showHighlightPrompt.value = false;
}
async function removeHighlight(h) {
    try {
        await axios.delete(route('reader.highlights.destroy', { book: props.book.id, highlight: h.id }));
        highlightList.value = highlightList.value.filter((x) => x.id !== h.id);
        if (rendition) { try { rendition.annotations.remove(h.location, 'highlight'); } catch (e) {} }
    } catch (e) { /* silent */ }
}

/* ---------------- In-book search (EPUB) ---------------- */
const showSearch = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const searching = ref(false);

async function runSearch() {
    const q = searchQuery.value.trim();
    if (!epubBook || !q) return;
    searching.value = true;
    searchResults.value = [];
    try {
        const results = await Promise.all(
            epubBook.spine.spineItems.map((item) =>
                item.load(epubBook.load.bind(epubBook))
                    .then(item.find.bind(item, q))
                    .finally(() => item.unload())
            )
        );
        searchResults.value = [].concat(...results).slice(0, 100);
    } catch (e) {
        searchResults.value = [];
    } finally {
        searching.value = false;
    }
}
function goToResult(cfi) {
    if (rendition) rendition.display(cfi);
    showSearch.value = false;
}

/* ---------------- Navigation ---------------- */
function goTo(target) {
    if (props.book.type === 'epub' && rendition) {
        rendition.display(target);
    } else if (props.book.type === 'pdf') {
        const n = parseInt(target, 10);
        if (!isNaN(n)) renderPdfPage(n);
    }
}
function epubPrev() { if (rendition) rendition.prev(); }
function epubNext() { if (rendition) rendition.next(); }

/* ---------------- Theme/pref watchers ---------------- */
watch(() => [prefs.theme, prefs.font_size, prefs.font_family, prefs.line_height, prefs.margin], () => {
    if (props.book.type === 'epub') applyEpubPrefs();
    savePrefs();
});

onMounted(async () => {
    try {
        if (props.book.type === 'epub') await initEpub();
        else if (props.book.type === 'pdf') await initPdf();
        else { errorMsg.value = 'Format file tidak didukung.'; loading.value = false; }
    } catch (e) {
        errorMsg.value = 'Gagal memuat buku: ' + (e?.message || 'unknown error');
        loading.value = false;
    }
});

onBeforeUnmount(() => {
    clearTimeout(saveTimer);
    clearTimeout(prefTimer);
    try { epubBook && epubBook.destroy(); } catch (e) {}
    try { pdfDoc && pdfDoc.destroy(); } catch (e) {}
});
</script>

<template>
    <Head :title="'Baca: ' + book.title" />

    <div :class="['flex h-screen flex-col', themeClasses[prefs.theme]]">
        <header class="sticky top-3 z-40 px-3">
            <nav
                class="mx-auto flex max-w-5xl items-center justify-between rounded-full border px-3 py-2 backdrop-blur-sm"
                :class="panelClass"
                aria-label="Kontrol pembaca"
            >
                <div class="flex min-w-0 items-center gap-space-sm">
                    <Link
                        :href="route('catalog.show', book.slug)"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full transition-colors"
                        :class="pillHover"
                        aria-label="Kembali"
                    >
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </Link>
                    <div class="min-w-0">
                        <p class="truncate text-body-sm font-semibold">{{ book.title }}</p>
                        <p class="hidden truncate text-caption opacity-60 sm:block">{{ book.author_name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <Transition
                        enter-active-class="transition ease-in-out duration-200"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out duration-200"
                        leave-to-class="opacity-0"
                    >
                        <span v-if="savedIndicator" class="mr-1 hidden text-caption opacity-60 sm:inline">tersimpan</span>
                    </Transition>
                    <span class="mr-1 text-caption font-semibold sm:mr-2">{{ Math.round(percent) }}%</span>

                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full transition-colors"
                        :class="pillHover"
                        title="Tambah bookmark"
                        aria-label="Tambah bookmark"
                        @click="addBookmark"
                    >
                        <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                    </button>
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full transition-colors"
                        :class="[pillHover, showBookmarks ? pillBg : '']"
                        title="Daftar bookmark"
                        aria-label="Daftar bookmark"
                        @click="showBookmarks = !showBookmarks; if (showBookmarks) showSettings = false"
                    >
                        <span class="material-symbols-outlined text-[20px]">bookmarks</span>
                    </button>
                    <button
                        v-if="book.type === 'epub'"
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full transition-colors"
                        :class="[pillHover, showToc ? pillBg : '']"
                        title="Daftar isi"
                        aria-label="Daftar isi"
                        @click="showToc = !showToc; if (showToc) showBookmarks = false"
                    >
                        <span class="material-symbols-outlined text-[20px]">list</span>
                    </button>
                    <button
                        v-if="book.type === 'epub'"
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full transition-colors"
                        :class="[pillHover, showSearch ? pillBg : '']"
                        title="Cari dalam buku"
                        aria-label="Cari dalam buku"
                        @click="showSearch = !showSearch; showSettings = false"
                    >
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </button>
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full transition-colors"
                        :class="[pillHover, showSettings ? pillBg : '']"
                        title="Pengaturan baca"
                        aria-label="Pengaturan baca"
                        @click="showSettings = !showSettings; if (showSettings) showSearch = false"
                    >
                        <span class="material-symbols-outlined text-[20px]">format_size</span>
                    </button>
                </div>
            </nav>
        </header>

        <div v-if="isSample" class="mx-auto mt-3 flex max-w-5xl items-center justify-between gap-space-sm rounded-[20px] bg-accent px-space-md py-space-sm text-white">
            <span class="flex items-center gap-2 text-body-sm">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
                Anda membaca <strong>pratinjau sampel</strong>.
            </span>
            <Link
                :href="route('catalog.show', book.slug)"
                class="rounded-full bg-white px-space-md py-1 text-caption font-semibold text-accent transition-opacity hover:opacity-90"
            >
                Beli buku penuh
            </Link>
        </div>

        <div class="h-1 w-full" :class="isDark ? 'bg-white/10' : 'bg-black/10'">
            <div class="h-1 transition-all" :class="isDark ? 'bg-white' : 'bg-ink'" :style="{ width: percent + '%' }"></div>
        </div>

        <main class="relative flex-1 overflow-hidden">
            <div v-if="loading" class="flex h-full flex-col items-center justify-center gap-space-sm opacity-60">
                <span class="material-symbols-outlined animate-pulse text-[32px]">auto_stories</span>
                <span class="text-body-sm">Memuat buku…</span>
            </div>
            <div v-else-if="errorMsg" class="flex h-full items-center justify-center p-8 text-center">
                <div class="max-w-md space-y-space-sm">
                    <span class="material-symbols-outlined text-[32px]">error</span>
                    <p class="text-body-md">{{ errorMsg }}</p>
                </div>
            </div>

            <div v-show="!loading && !errorMsg && book.type === 'epub'" ref="viewer" class="h-full w-full"></div>

            <div v-show="!loading && !errorMsg && book.type === 'pdf'" class="flex h-full flex-col items-center overflow-auto p-4" :class="isDark ? 'bg-white/5' : 'bg-black/5'">
                <canvas ref="canvas" class="max-w-full rounded-sm shadow-sm"></canvas>
                <div class="sticky bottom-4 mt-4 flex items-center gap-space-sm rounded-full border px-space-md py-2 backdrop-blur-sm" :class="panelClass">
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" :disabled="pdfPage <= 1" aria-label="Halaman sebelumnya" @click="pdfPrev">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <span class="text-body-sm font-semibold">Hal. {{ pdfPage }} / {{ pdfTotal }}</span>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Halaman berikutnya" @click="pdfNext">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>

            <div v-if="!loading && !errorMsg && book.type === 'epub'" class="pointer-events-none absolute inset-y-0 flex w-full items-center justify-between">
                <button type="button" class="pointer-events-auto ml-2 grid h-14 w-10 place-items-center rounded-full backdrop-blur-sm transition-colors" :class="[pillBg, pillHover]" aria-label="Halaman sebelumnya" @click="epubPrev">
                    <span class="material-symbols-outlined text-[24px]">chevron_left</span>
                </button>
                <button type="button" class="pointer-events-auto mr-2 grid h-14 w-10 place-items-center rounded-full backdrop-blur-sm transition-colors" :class="[pillBg, pillHover]" aria-label="Halaman berikutnya" @click="epubNext">
                    <span class="material-symbols-outlined text-[24px]">chevron_right</span>
                </button>
            </div>

            <div v-if="showSettings" class="absolute right-4 top-4 w-72 rounded-[24px] border p-space-md shadow-lg" :class="panelClass">
                <div class="flex items-center justify-between border-b pb-space-sm" :class="isDark ? 'border-white/10' : 'border-hairline-soft'">
                    <h3 class="text-title-md">Pengaturan Baca</h3>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Tutup" @click="showSettings = false">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <label class="mt-space-md block text-caption opacity-70">Tema</label>
                <div class="mt-1 mb-space-md grid grid-cols-3 gap-1 rounded-full p-1" :class="isDark ? 'bg-white/10' : 'bg-black/5'">
                    <button v-for="t in ['light', 'sepia', 'dark']" :key="t" type="button"
                        :class="['rounded-full px-2 py-1 text-caption capitalize transition-colors', prefs.theme === t ? (isDark ? 'bg-white text-ink' : 'bg-canvas text-ink shadow-sm') : 'opacity-70 hover:opacity-100']"
                        @click="prefs.theme = t">{{ t }}</button>
                </div>

                <label class="block text-caption opacity-70">Ukuran huruf: {{ prefs.font_size }}px</label>
                <input v-model.number="prefs.font_size" type="range" min="12" max="28" class="mt-1 mb-space-md w-full accent-current">

                <label class="block text-caption opacity-70">Jenis huruf</label>
                <div class="mt-1 mb-space-md grid grid-cols-2 gap-1 rounded-full p-1" :class="isDark ? 'bg-white/10' : 'bg-black/5'">
                    <button v-for="f in ['serif', 'sans-serif']" :key="f" type="button"
                        :class="['rounded-full px-2 py-1 text-caption transition-colors', prefs.font_family === f ? (isDark ? 'bg-white text-ink' : 'bg-canvas text-ink shadow-sm') : 'opacity-70 hover:opacity-100']"
                        @click="prefs.font_family = f">{{ f === 'serif' ? 'Serif' : 'Sans-serif' }}</button>
                </div>

                <label class="block text-caption opacity-70">Warna highlight</label>
                <div class="mt-1 mb-space-md flex gap-2">
                    <button v-for="c in ['yellow', 'green', 'blue', 'pink']" :key="c" type="button"
                        class="h-7 w-7 rounded-full border-2 transition-transform hover:scale-110"
                        :class="prefs.highlight_color === c ? 'border-current' : 'border-transparent'"
                        :style="{ background: colorHex(c) }" :aria-label="'Warna ' + c" @click="prefs.highlight_color = c"></button>
                </div>

                <template v-if="book.type === 'epub'">
                    <label class="block text-caption opacity-70">Tinggi baris: {{ prefs.line_height }}</label>
                    <input v-model.number="prefs.line_height" type="range" min="1.2" max="2.5" step="0.1" class="mt-1 mb-space-md w-full accent-current">

                    <label class="block text-caption opacity-70">Lebar halaman</label>
                    <div class="mt-1 grid grid-cols-3 gap-1 rounded-full p-1" :class="isDark ? 'bg-white/10' : 'bg-black/5'">
                        <button v-for="m in ['narrow', 'normal', 'wide']" :key="m" type="button"
                            :class="['rounded-full px-2 py-1 text-caption capitalize transition-colors', prefs.margin === m ? (isDark ? 'bg-white text-ink' : 'bg-canvas text-ink shadow-sm') : 'opacity-70 hover:opacity-100']"
                            @click="prefs.margin = m">{{ m }}</button>
                    </div>
                </template>
            </div>

            <div v-if="showSearch" class="absolute right-4 top-4 w-80 rounded-[24px] border p-space-md shadow-lg" :class="panelClass">
                <div class="flex items-center justify-between border-b pb-space-sm" :class="isDark ? 'border-white/10' : 'border-hairline-soft'">
                    <h3 class="text-title-md">Cari dalam buku</h3>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Tutup" @click="showSearch = false">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <form class="mt-space-md flex gap-space-sm" @submit.prevent="runSearch">
                    <input v-model="searchQuery" type="text" placeholder="Kata kunci…" class="field min-w-0 flex-1">
                    <button type="submit" class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-ink text-white transition-opacity hover:opacity-90" aria-label="Cari">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                    </button>
                </form>
                <p v-if="searching" class="mt-space-sm text-caption opacity-70">Mencari…</p>
                <p v-else-if="searchQuery && !searchResults.length" class="mt-space-sm text-caption opacity-70">Tidak ada hasil.</p>
                <ul class="mt-space-sm max-h-64 space-y-space-sm overflow-auto">
                    <li v-for="(r, i) in searchResults" :key="i">
                        <button type="button" class="w-full rounded-sm border p-2 text-left text-body-sm transition-colors hover:opacity-80"
                            :class="isDark ? 'border-white/10' : 'border-hair'" v-html="r.excerpt" @click="goToResult(r.cfi)"></button>
                    </li>
                </ul>
            </div>

            <div v-if="showToc" class="absolute left-0 top-0 h-full w-80 overflow-auto rounded-r-[24px] border-r p-space-md shadow-lg" :class="panelClass">
                <div class="flex items-center justify-between border-b pb-space-sm" :class="isDark ? 'border-white/10' : 'border-hairline-soft'">
                    <h3 class="text-title-md">Daftar Isi</h3>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Tutup" @click="showToc = false">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <ul class="mt-space-sm space-y-1">
                    <li v-for="(item, i) in toc" :key="i">
                        <button type="button" class="flex w-full items-center gap-space-sm rounded-full px-space-sm py-2 text-left text-body-sm transition-colors" :class="pillHover" @click="goTo(item.href); showToc = false">
                            <span class="material-symbols-outlined text-[16px] opacity-60">bookmark</span>
                            <span class="truncate">{{ item.label }}</span>
                        </button>
                    </li>
                </ul>
                <p v-if="!toc.length" class="mt-space-sm text-caption opacity-70">Daftar isi tidak tersedia.</p>
            </div>

            <div v-if="showBookmarks" class="absolute right-0 top-0 h-full w-80 overflow-auto rounded-l-[24px] border-l p-space-md shadow-lg" :class="panelClass">
                <div class="flex items-center justify-between border-b pb-space-sm" :class="isDark ? 'border-white/10' : 'border-hairline-soft'">
                    <h3 class="text-title-md">Bookmark</h3>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Tutup" @click="showBookmarks = false">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <p v-if="!bookmarkList.length" class="mt-space-sm text-caption opacity-70">Belum ada bookmark.</p>
                <ul class="mt-space-sm space-y-space-sm">
                    <li v-for="b in bookmarkList" :key="b.id" class="flex items-center justify-between gap-space-sm rounded-xl border p-space-sm" :class="isDark ? 'border-white/10' : 'border-hair'">
                        <button type="button" class="min-w-0 flex-1 truncate text-left text-body-sm" @click="goToBookmark(b)">
                            {{ b.label || 'Tanpa label' }}
                        </button>
                        <button type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Hapus bookmark" @click="removeBookmark(b.id)">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </li>
                </ul>

                <div class="mt-space-lg flex items-center justify-between border-b pb-space-sm" :class="isDark ? 'border-white/10' : 'border-hairline-soft'">
                    <h3 class="text-title-md">Highlight ({{ highlightList.length }})</h3>
                </div>
                <p v-if="!highlightList.length" class="mt-space-sm text-caption opacity-70">Belum ada sorotan.</p>
                <ul class="mt-space-sm space-y-space-sm">
                    <li v-for="h in highlightList" :key="h.id" class="rounded-xl border p-space-sm" :class="isDark ? 'border-white/10' : 'border-hair'">
                        <div class="flex items-start justify-between gap-space-sm">
                            <button type="button" class="min-w-0 flex-1 truncate text-left text-body-sm" :style="{ borderLeft: '4px solid ' + colorHex(h.color), paddingLeft: '8px' }" @click="goTo(h.location); showBookmarks = false">
                                {{ h.text }}
                            </button>
                            <button type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded-full transition-colors" :class="pillHover" aria-label="Hapus sorotan" @click="removeHighlight(h)">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-if="showHighlightPrompt" class="absolute bottom-6 left-1/2 w-[min(92vw,26rem)] -translate-x-1/2 rounded-[24px] bg-ink px-space-md py-space-md text-white shadow-xl">
                <div class="flex items-start gap-space-sm">
                    <span class="material-symbols-outlined text-[18px] text-accent">format_ink</span>
                    <p class="min-w-0 flex-1 text-body-sm">“{{ pendingHighlight?.text }}”</p>
                </div>
                <div class="mt-space-md flex justify-end gap-space-sm">
                    <button type="button" class="rounded-full border border-white/20 px-space-md py-1.5 text-caption transition-colors hover:bg-white/10" @click="cancelHighlight">Batal</button>
                    <button type="button" class="rounded-full bg-canvas px-space-md py-1.5 text-caption font-semibold text-ink transition-opacity hover:opacity-90" @click="confirmHighlight">Highlight</button>
                </div>
            </div>
        </main>
    </div>
</template>
