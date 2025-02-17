<script setup>
defineProps({
    type: {
        type: String,
        default: "submit",
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-yellow-400 border border-transparent rounded font-semibold text-xs text-black uppercase tracking-widest hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-colors duration-200"
    >
        <slot />
    </button>
</template>
