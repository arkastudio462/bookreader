<script setup>
defineProps({
    for: String,
    value: String,
});
</script>

<template>
    <label :for="for" class="lk mb-2 block text-sm text-ink">
        {{ value }}
    </label>
</template>
