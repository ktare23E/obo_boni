<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center gap-4 px-4 py-2 text-sm font-medium rounded-lg bg-[#0F766E] text-white shadow-md'
        : 'flex items-center gap-4 px-4 py-2 text-sm font-medium rounded-lg hover:bg-[#0F766E]/80 text-gray-200',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
