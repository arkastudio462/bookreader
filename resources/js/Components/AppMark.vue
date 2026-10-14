<script setup>
defineProps({
    title: { type: String, default: '' },
});
</script>

<template>
    <span class="lk inline-flex h-8 w-8 items-center justify-center rounded-[30%] bg-ink text-sm text-white">
        {{ title || 'P' }}
    </span>
</template>
