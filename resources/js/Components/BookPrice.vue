<script setup>
import { computed } from 'vue';

const props = defineProps({
    book: { type: Object, required: true },
});

const onPromo = computed(
    () => props.book.on_promo === true || (props.book.discount_source && props.book.discount_source !== 'none'),
);
const effective = computed(() => Number(props.book.effective_price ?? props.book.price));
const base = computed(() => Number(props.book.base_price ?? props.book.price));
const label = computed(() => (props.book.discount_source === 'flash' ? 'Flash Sale' : 'Diskon'));
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <span class="font-semibold text-blue-600">
            {{ effective > 0 ? 'Rp ' + effective.toLocaleString('id-ID') : 'Gratis' }}
        </span>
        <template v-if="onPromo">
            <span class="text-sm text-gray-400 line-through">Rp {{ base.toLocaleString('id-ID') }}</span>
            <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-600">
                {{ label }} {{ book.discount_percent }}%
            </span>
        </template>
    </div>
</template>
