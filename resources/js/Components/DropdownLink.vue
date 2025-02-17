<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <button
            v-if="as == 'button'"
            type="submit"
            class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-100 hover:text-yellow-400 hover:bg-gray-700 focus:outline-none transition-colors duration-200"
        >
            <slot />
        </button>

        <Link
            v-else-if="as == 'a'"
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-100 hover:text-yellow-400 hover:bg-gray-700 focus:outline-none transition-colors duration-200"
        >
            <slot />
        </Link>

        <Link
            v-else
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-100 hover:text-yellow-400 hover:bg-gray-700 focus:outline-none transition-colors duration-200"
        >
            <slot />
        </Link>
    </div>
</template>
