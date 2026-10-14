<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <p v-show="message" class="lk mt-2 text-sm text-ink">
        {{ message }}
    </p>
</template>
