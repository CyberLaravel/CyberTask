<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mt-6"
    >
        <h3 class="text-xl font-bold text-yellow-400 mb-4">
            Activity Timeline
        </h3>

        <div class="space-y-4">
            <div
                v-for="activity in activities"
                :key="activity.id"
                class="relative pl-8 pb-4"
            >
                <!-- Timeline line -->
                <div
                    class="absolute left-3 top-0 bottom-0 w-0.5 bg-yellow-400"
                ></div>

                <!-- Timeline dot -->
                <div
                    class="absolute left-2 top-2 w-2.5 h-2.5 rounded-full bg-yellow-400"
                ></div>

                <!-- Activity content -->
                <div
                    class="bg-gray-900 rounded-lg p-4 border border-yellow-400"
                >
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-yellow-400 font-bold">
                                {{ activity.user.name }}
                            </span>
                            <span class="text-gray-400 ml-2">
                                {{ activity.action }}
                            </span>
                        </div>
                        <span class="text-gray-500 text-sm">
                            {{ formatDate(activity.created_at) }}
                        </span>
                    </div>
                    <p class="text-gray-100">{{ activity.description }}</p>

                    <!-- Changes -->
                    <div v-if="activity.changes" class="mt-2 text-sm space-y-1">
                        <div
                            v-for="(newValue, field) in activity.changes"
                            :key="field"
                            class="text-gray-400"
                        >
                            <span class="text-blue-300">{{
                                formatField(field)
                            }}</span>
                            changed to
                            <span class="text-yellow-400">{{
                                formatValue(newValue)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    activities: {
        type: Array,
        required: true,
    },
});

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

const formatField = (field) => {
    return field
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

const formatValue = (value) => {
    if (value === null) return "none";
    if (typeof value === "boolean") return value ? "yes" : "no";
    if (typeof value === "object") return JSON.stringify(value);
    return value.toString();
};
</script>
