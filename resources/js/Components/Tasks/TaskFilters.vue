<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mb-6"
    >
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-yellow-400 mb-2">Search</label>
                <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Search tasks..."
                    class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400 placeholder-gray-500"
                />
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-yellow-400 mb-2">Status</label>
                <select
                    v-model="filters.status"
                    class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-yellow-400 mb-2">Priority</label>
                <select
                    v-model="filters.priority"
                    class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                >
                    <option value="">All Priorities</option>
                    <option value="0">Low</option>
                    <option value="1">Medium</option>
                    <option value="2">High</option>
                </select>
            </div>
        </div>

        <!-- Tags Filter -->
        <div class="mt-4">
            <label class="block text-yellow-400 mb-2">Tags</label>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tag in availableTags"
                    :key="tag.id"
                    @click="toggleTag(tag.id)"
                    class="px-3 py-1 rounded-full text-sm transition-colors"
                    :class="
                        filters.tags.includes(tag.id)
                            ? 'bg-yellow-400 text-black'
                            : 'bg-gray-700 text-gray-300'
                    "
                >
                    {{ tag.name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    availableTags: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["update:filters"]);

const filters = ref({
    search: "",
    status: "",
    priority: "",
    tags: [],
});

const toggleTag = (tagId) => {
    const index = filters.value.tags.indexOf(tagId);
    if (index === -1) {
        filters.value.tags.push(tagId);
    } else {
        filters.value.tags.splice(index, 1);
    }
};

watch(
    filters,
    (newFilters) => {
        emit("update:filters", newFilters);
    },
    { deep: true }
);
</script>
