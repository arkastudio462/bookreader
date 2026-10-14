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
        <!-- Toolbar -->
        <header class="flex items-center justify-between border-b border-black/10 px-4 py-2 text-sm">
            <div class="flex items-center gap-3">
                <Link :href="route('catalog.show', book.slug)" class="lk hover:opacity-70">← Kembali</Link>
                <span class="lk">{{ book.title }}</span>
                <span class="hidden sm:inline opacity-60">· {{ book.author_name }}</span>
            </div>

            <div class="flex items-center gap-2">
                <span v-if="savedIndicator" class="text-xs opacity-60">tersimpan ✓</span>
                <span class="text-xs opacity-60">{{ Math.round(percent) }}%</span>
                <button @click="addBookmark" class="rounded px-2 py-1 hover:bg-black/5" title="Tambah bookmark">🔖</button>
                <button @click="showBookmarks = !showBookmarks" class="rounded px-2 py-1 hover:bg-black/5" title="Daftar bookmark">📑</button>
                <button v-if="book.type === 'epub'" @click="showToc = !showToc" class="rounded px-2 py-1 hover:bg-black/5" title="Daftar isi">☰</button>
                <button v-if="book.type === 'epub'" @click="showSearch = !showSearch" class="rounded px-2 py-1 hover:bg-black/5" title="Cari dalam buku">🔍</button>
                <button @click="showSettings = !showSettings" class="rounded px-2 py-1 hover:bg-black/5" title="Pengaturan">Aa</button>
            </div>
        </header>

        <!-- Sample banner -->
        <div v-if="isSample" class="bg-soft px-4 py-2 text-center text-sm text-ink">
            Anda membaca <strong>pratinjau sampel</strong>.
            <Link :href="route('catalog.show', book.slug)" class="lk underline">Beli buku untuk membaca penuh</Link>
        </div>

        <!-- Progress bar -->
        <div class="h-1 w-full bg-black/10">
            <div class="h-1 bg-ink transition-all" :style="{ width: percent + '%' }"></div>
        </div>

        <!-- Content -->
        <main class="relative flex-1 overflow-hidden">
            <div v-if="loading" class="flex h-full items-center justify-center opacity-60">Memuat buku…</div>
            <div v-else-if="errorMsg" class="flex h-full items-center justify-center p-8 text-center text-ink">{{ errorMsg }}</div>

            <!-- EPUB viewer -->
            <div v-show="!loading && !errorMsg && book.type === 'epub'" ref="viewer" class="h-full w-full"></div>

            <!-- PDF viewer -->
            <div v-show="!loading && !errorMsg && book.type === 'pdf'" class="flex h-full flex-col items-center overflow-auto bg-black/5 p-4">
                <canvas ref="canvas" class="max-w-full"></canvas>
                <div class="sticky bottom-0 mt-4 flex items-center gap-4 rounded-full bg-canvas/90 px-4 py-2">
                    <button @click="pdfPrev" :disabled="pdfPage <= 1" class="rounded px-3 py-1 disabled:opacity-30">‹</button>
                    <span class="text-sm">Hal. {{ pdfPage }} / {{ pdfTotal }}</span>
                    <button @click="pdfNext" class="rounded px-3 py-1">›</button>
                </div>
            </div>

            <!-- EPUB nav arrows -->
            <div v-if="!loading && !errorMsg && book.type === 'epub'" class="pointer-events-none absolute inset-y-0 flex w-full items-center justify-between">
                <button @click="epubPrev" class="pointer-events-auto ml-2 rounded-full bg-black/10 px-3 py-4 hover:bg-black/20">‹</button>
                <button @click="epubNext" class="pointer-events-auto mr-2 rounded-full bg-black/10 px-3 py-4 hover:bg-black/20">›</button>
            </div>

            <!-- Settings panel -->
            <div v-if="showSettings" class="absolute right-4 top-4 w-72 rounded-md border border-hairline-soft bg-canvas p-4 text-ink">
                <h3 class="h mb-3">Pengaturan Baca</h3>
                <label class="mb-1 block text-xs">Tema</label>
                <div class="mb-3 flex gap-2">
                    <button v-for="t in ['light','sepia','dark']" :key="t" @click="prefs.theme = t"
                        :class="['flex-1 rounded border px-2 py-1 text-xs capitalize', prefs.theme === t ? 'seg-on border-transparent' : 'seg-off border-hair']">{{ t }}</button>
                </div>
                <label class="mb-1 block text-xs">Ukuran huruf: {{ prefs.font_size }}px</label>
                <input v-model.number="prefs.font_size" type="range" min="12" max="28" class="mb-3 w-full">
                <label class="mb-1 block text-xs">Jenis huruf</label>
                <div class="mb-3 flex gap-2">
                    <button v-for="f in ['serif','sans-serif']" :key="f" @click="prefs.font_family = f"
                        :class="['flex-1 rounded border px-2 py-1 text-xs', prefs.font_family === f ? 'seg-on border-transparent' : 'seg-off border-hair']">{{ f }}</button>
                </div>
                <label class="mb-1 block text-xs">Warna highlight</label>
                <div class="flex gap-2">
                    <button v-for="c in ['yellow','green','blue','pink']" :key="c" @click="prefs.highlight_color = c"
                        class="h-6 w-6 rounded-full border" :style="{ background: colorHex(c) }"></button>
                </div>

                <template v-if="book.type === 'epub'">
                    <label class="mb-1 mt-3 block text-xs">Tinggi baris: {{ prefs.line_height }}</label>
                    <input v-model.number="prefs.line_height" type="range" min="1.2" max="2.5" step="0.1" class="mb-3 w-full">
                    <label class="mb-1 block text-xs">Lebar halaman</label>
                    <div class="flex gap-2">
                        <button v-for="m in ['narrow','normal','wide']" :key="m" @click="prefs.margin = m"
                            :class="['flex-1 rounded border px-2 py-1 text-xs capitalize', prefs.margin === m ? 'seg-on border-transparent' : 'seg-off border-hair']">{{ m }}</button>
                    </div>
                </template>
            </div>

            <!-- Search panel (EPUB) -->
            <div v-if="showSearch" class="absolute right-4 top-16 w-80 rounded-md border border-hairline-soft bg-canvas p-4 text-ink">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="h">Cari dalam buku</h3>
                    <button @click="showSearch = false" class="lk text-muted">✕</button>
                </div>
                <form @submit.prevent="runSearch" class="mb-3 flex gap-2">
                    <input v-model="searchQuery" type="text" placeholder="Kata kunci…"
                        class="field flex-1">
                    <button type="submit" class="rounded-full bg-ink px-3 py-1 text-sm text-white">Cari</button>
                </form>
                <p v-if="searching" class="text-sm text-muted">Mencari…</p>
                <p v-else-if="searchQuery && !searchResults.length" class="text-sm text-muted">Tidak ada hasil.</p>
                <ul class="max-h-64 space-y-2 overflow-auto text-sm">
                    <li v-for="(r, i) in searchResults" :key="i">
                        <button @click="goToResult(r.cfi)" class="w-full rounded-sm border border-hair p-2 text-left hover:bg-soft"
                            v-html="r.excerpt"></button>
                    </li>
                </ul>
            </div>

            <!-- TOC panel -->
            <div v-if="showToc" class="absolute left-0 top-0 h-full w-80 overflow-auto border-r border-hairline-soft bg-canvas p-4 text-ink">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="h">Daftar Isi</h3>
                    <button @click="showToc = false" class="lk text-muted">✕</button>
                </div>
                <ul class="space-y-1 text-sm">
                    <li v-for="(item, i) in toc" :key="i">
                        <button @click="goTo(item.href); showToc = false" class="w-full rounded-sm px-2 py-1 text-left hover:bg-soft">{{ item.label }}</button>
                    </li>
                </ul>
            </div>

            <!-- Bookmarks panel -->
            <div v-if="showBookmarks" class="absolute right-0 top-0 h-full w-80 overflow-auto border-l border-hairline-soft bg-canvas p-4 text-ink">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="h">Bookmark</h3>
                    <button @click="showBookmarks = false" class="lk text-muted">✕</button>
                </div>
                <p v-if="!bookmarkList.length" class="text-sm text-muted">Belum ada bookmark.</p>
                <ul class="space-y-2 text-sm">
                    <li v-for="b in bookmarkList" :key="b.id" class="flex items-center justify-between rounded-sm border border-hair p-2">
                        <button @click="goToBookmark(b)" class="text-left">{{ b.label || 'Tanpa label' }}</button>
                        <button @click="removeBookmark(b.id)" class="lk text-muted">Hapus</button>
                    </li>
                </ul>

                <h3 class="h mb-3 mt-6">Highlight ({{ highlightList.length }})</h3>
                <ul class="space-y-2 text-sm">
                    <li v-for="h in highlightList" :key="h.id" class="rounded-sm border border-hair p-2">
                        <div class="flex items-start justify-between">
                            <button @click="goTo(h.location); showBookmarks = false" class="text-left" :style="{ borderLeft: '4px solid ' + colorHex(h.color), paddingLeft: '6px' }">{{ h.text }}</button>
                            <button @click="removeHighlight(h)" class="lk ml-2 text-muted">Hapus</button>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Highlight prompt -->
            <div v-if="showHighlightPrompt" class="absolute bottom-6 left-1/2 -translate-x-1/2 rounded-md bg-ink px-4 py-3 text-white">
                <p class="mb-2 max-w-sm truncate text-sm">"{{ pendingHighlight?.text }}"</p>
                <div class="flex gap-2">
                    <button @click="confirmHighlight" class="rounded-full bg-canvas px-3 py-1 text-sm text-ink">Highlight</button>
                    <button @click="cancelHighlight" class="rounded-full bg-ink-soft px-3 py-1 text-sm text-white">Batal</button>
                </div>
            </div>
        </main>
    </div>
</template>
