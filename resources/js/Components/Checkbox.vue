<script setup>
import { computed } from 'vue';

const props = defineProps({
    checked: Boolean,
    name: String,
    disabled: Boolean,
});

const emit = defineEmits(['update:checked']);

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        v-model="proxyChecked"
        type="checkbox"
        :name="name"
        :disabled="disabled"
        class="h-4 w-4 rounded-full border-hair bg-canvas text-ink focus:ring-2 focus:ring-ink focus:ring-offset-0 disabled:opacity-50"
    />
</template>
