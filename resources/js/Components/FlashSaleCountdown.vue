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
const parts = computed(() => {
    const total = Math.floor(remaining.value / 1000);
    return {
        d: Math.floor(total / 86400),
        h: String(Math.floor((total % 86400) / 3600)).padStart(2, '0'),
        m: String(Math.floor((total % 3600) / 60)).padStart(2, '0'),
        s: String(total % 60).padStart(2, '0'),
    };
});
</script>

<template>
    <div class="flex items-center gap-1">
        <template v-if="!expired">
            <span v-if="parts.d > 0" class="rounded bg-black/80 px-2 py-1 text-sm font-bold text-white">{{ parts.d }}h</span>
            <span class="rounded bg-black/80 px-2 py-1 text-sm font-bold text-white">{{ parts.h }}</span>
            <span class="font-bold">:</span>
            <span class="rounded bg-black/80 px-2 py-1 text-sm font-bold text-white">{{ parts.m }}</span>
            <span class="font-bold">:</span>
            <span class="rounded bg-black/80 px-2 py-1 text-sm font-bold text-white">{{ parts.s }}</span>
        </template>
        <span v-else class="text-sm font-semibold">Promo berakhir</span>
    </div>
</template>
