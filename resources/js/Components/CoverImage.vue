<script setup>
defineProps({
    book: { type: Object, required: true },
});

function hashCode(str) {
    let h = 0;
    for (let i = 0; i < str.length; i++) {
        h = (Math.imul(31, h) + str.charCodeAt(i)) | 0;
    }
    return Math.abs(h);
}
</script>

<template>
    <div
        class="relative aspect-[3/4] overflow-hidden rounded-sm"
        :style="{
            background: `hsl(${hashCode(book.title) % 360} 45% 32%)`,
            color: `hsl(${hashCode(book.title) % 360} 60% 88%)`,
        }"
    >
        <svg
            class="absolute inset-0 h-full w-full opacity-30"
            viewBox="0 0 90 120"
            preserveAspectRatio="none"
            aria-hidden="true"
        >
            <circle cx="70" cy="30" r="34" :fill="`hsl(${hashCode(book.title) % 360} 60% 88%)`" />
            <circle
                cx="20"
                cy="110"
                r="28"
                fill="none"
                :stroke="`hsl(${hashCode(book.title) % 360} 60% 88%)`"
                stroke-width="3"
            />
        </svg>
        <div class="relative flex h-full flex-col justify-between p-3">
            <p class="h text-[15px] leading-tight">{{ book.title }}</p>
            <p class="text-[11px] opacity-80">{{ book.author_name }}</p>
        </div>
    </div>
</template>
