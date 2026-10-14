<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: [String, Number],
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="field w-full"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
