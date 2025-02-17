<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Task Statistics -->
        <div
            v-for="stat in statistics"
            :key="stat.title"
            class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon hover:shadow-neon-lg transition-all duration-300"
        >
            <h3 class="text-blue-300 text-lg font-semibold mb-2">
                {{ stat.title }}
            </h3>
            <div class="flex items-end space-x-2">
                <span class="text-4xl font-bold text-yellow-400">
                    {{ stat.value }}
                </span>
                <span class="text-gray-400 mb-1">{{ stat.label }}</span>
            </div>
            <div
                class="w-full bg-gray-700 rounded-full h-2 mt-4 overflow-hidden"
            >
                <div
                    class="bg-yellow-400 h-full transition-all duration-500"
                    :style="{ width: `${stat.percentage}%` }"
                ></div>
            </div>
        </div>
    </div>

    <!-- Priority Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div
            class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
        >
            <h3 class="text-xl font-bold text-yellow-400 mb-4">
                Priority Distribution
            </h3>
            <div class="space-y-4">
                <div
                    v-for="priority in priorityDistribution"
                    :key="priority.label"
                    class="space-y-2"
                >
                    <div class="flex justify-between text-gray-100">
                        <span>{{ priority.label }}</span>
                        <span>{{ priority.count }} tasks</span>
                    </div>
                    <div
                        class="w-full bg-gray-700 rounded-full h-2 overflow-hidden"
                    >
                        <div
                            class="h-full transition-all duration-500"
                            :class="priority.color"
                            :style="{ width: `${priority.percentage}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Due Date Overview -->
        <div
            class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
        >
            <h3 class="text-xl font-bold text-yellow-400 mb-4">
                Due Date Overview
            </h3>
            <div class="space-y-4">
                <div
                    v-for="category in dueDateCategories"
                    :key="category.label"
                    class="flex justify-between items-center"
                >
                    <span class="text-gray-100">{{ category.label }}</span>
                    <span
                        class="px-3 py-1 rounded-full text-sm"
                        :class="category.color"
                    >
                        {{ category.count }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const statistics = computed(() => [
    {
        title: "Total Tasks",
        value: props.tasks.length,
        label: "tasks",
        percentage: 100,
    },
    {
        title: "Completed Tasks",
        value: props.tasks.filter((t) => t.status === "completed").length,
        label: "completed",
        percentage:
            (props.tasks.filter((t) => t.status === "completed").length /
                props.tasks.length) *
            100,
    },
    {
        title: "In Progress",
        value: props.tasks.filter((t) => t.status === "in-progress").length,
        label: "active",
        percentage:
            (props.tasks.filter((t) => t.status === "in-progress").length /
                props.tasks.length) *
            100,
    },
]);

const priorityDistribution = computed(() => {
    const total = props.tasks.length;
    return [
        {
            label: "High Priority",
            count: props.tasks.filter((t) => t.priority === 2).length,
            percentage:
                (props.tasks.filter((t) => t.priority === 2).length / total) *
                100,
            color: "bg-red-400",
        },
        {
            label: "Medium Priority",
            count: props.tasks.filter((t) => t.priority === 1).length,
            percentage:
                (props.tasks.filter((t) => t.priority === 1).length / total) *
                100,
            color: "bg-yellow-400",
        },
        {
            label: "Low Priority",
            count: props.tasks.filter((t) => t.priority === 0).length,
            percentage:
                (props.tasks.filter((t) => t.priority === 0).length / total) *
                100,
            color: "bg-blue-400",
        },
    ];
});

const dueDateCategories = computed(() => {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const nextWeek = new Date(today);
    nextWeek.setDate(nextWeek.getDate() + 7);

    return [
        {
            label: "Overdue",
            count: props.tasks.filter(
                (t) => new Date(t.due_date) < today && t.status !== "completed"
            ).length,
            color: "bg-red-400 text-black",
        },
        {
            label: "Due Today",
            count: props.tasks.filter((t) => {
                const dueDate = new Date(t.due_date);
                return dueDate >= today && dueDate < tomorrow;
            }).length,
            color: "bg-yellow-400 text-black",
        },
        {
            label: "Due This Week",
            count: props.tasks.filter((t) => {
                const dueDate = new Date(t.due_date);
                return dueDate >= tomorrow && dueDate <= nextWeek;
            }).length,
            color: "bg-blue-400 text-black",
        },
    ];
});
</script>
