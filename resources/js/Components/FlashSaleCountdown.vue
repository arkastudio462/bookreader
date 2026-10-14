<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    endsAt: { type: String, required: true },
});

const now = ref(Date.now());
let timer = null;

onMounted(() => {
    timer = setInterval(() => (now.value = Date.now()), 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const remaining = computed(() => Math.max(0, new Date(props.endsAt).getTime() - now.value));
const expired = computed(() => remaining.value <= 0);
const pad = (n) => String(n).padStart(2, '0');
const parts = computed(() => {
    const total = Math.floor(remaining.value / 1000);
    return {
        h: pad(Math.floor(total / 3600)),
        m: pad(Math.floor((total % 3600) / 60)),
        s: pad(total % 60),
    };
});
</script>

<template>
    <div class="flex items-center gap-2">
        <template v-if="!expired">
            <div class="grid h-16 w-16 place-items-center rounded-sm bg-soft text-center">
                <div>
                    <b class="h block text-2xl">{{ parts.h }}</b>
                    <span class="text-xs text-muted">jam</span>
                </div>
            </div>
            <div class="grid h-16 w-16 place-items-center rounded-sm bg-soft text-center">
                <div>
                    <b class="h block text-2xl">{{ parts.m }}</b>
                    <span class="text-xs text-muted">menit</span>
                </div>
            </div>
            <div class="grid h-16 w-16 place-items-center rounded-sm bg-soft text-center">
                <div>
                    <b class="h block text-2xl">{{ parts.s }}</b>
                    <span class="text-xs text-muted">detik</span>
                </div>
            </div>
        </template>
        <span v-else class="lk text-sm text-muted">Promo berakhir</span>
    </div>
</template>
