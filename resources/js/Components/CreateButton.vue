<script setup>
    import { Link } from '@inertiajs/vue3';
    const props = defineProps({
        name : String,
        href : String
    })
</script>

<template>
    <Link :href="href" class="py-2 px-6 rounded-full text-white bg-black text-sm flex items-center gap-2">
    <slot></slot>
    {{ props.name }}
    </Link>
</template>