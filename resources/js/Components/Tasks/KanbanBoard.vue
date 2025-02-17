<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
    >
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-yellow-400 glitch-text">
                Kanban Board
            </h2>
            <button
                @click="$emit('view-change', 'list')"
                class="px-4 py-2 border border-yellow-400 text-yellow-400 rounded-md hover:bg-yellow-400 hover:text-black transition-colors duration-200"
            >
                Switch to List View
            </button>
        </div>

        <div class="flex space-x-6 overflow-x-auto pb-4">
            <div
                v-for="column in columns"
                :key="column.status"
                class="flex-shrink-0 w-80"
            >
                <!-- Column Header -->
                <div
                    class="bg-gray-900 rounded-t-lg p-3 border-t border-x border-yellow-400"
                >
                    <h3 class="text-lg font-semibold text-blue-300">
                        {{ column.title }}
                        <span class="text-gray-400 text-sm ml-2"
                            >({{ column.tasks.length }})</span
                        >
                    </h3>
                </div>

                <!-- Task List -->
                <div
                    class="bg-gray-900 min-h-[500px] rounded-b-lg p-3 border-b border-x border-yellow-400"
                >
                    <draggable
                        v-model="column.tasks"
                        :group="{ name: 'tasks' }"
                        item-key="id"
                        @change="handleDragChange($event, column.status)"
                        class="space-y-3"
                    >
                        <template #item="{ element: task }">
                            <div
                                class="bg-gray-800 rounded-lg p-4 border border-yellow-400 shadow-neon cursor-move hover:shadow-neon-lg transition-all duration-300"
                                @click="$emit('task-click', task)"
                            >
                                <div
                                    class="flex justify-between items-start mb-2"
                                >
                                    <h4 class="text-yellow-400 font-semibold">
                                        {{ task.title }}
                                    </h4>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="getPriorityClass(task.priority)"
                                    >
                                        {{ getPriorityLabel(task.priority) }}
                                    </span>
                                </div>

                                <p
                                    v-if="task.description"
                                    class="text-gray-400 text-sm mb-2 line-clamp-2"
                                >
                                    {{ task.description }}
                                </p>

                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span
                                        v-for="tag in task.tags"
                                        :key="tag.id"
                                        class="px-2 py-1 text-xs rounded-full"
                                        :style="{ backgroundColor: tag.color }"
                                    >
                                        {{ tag.name }}
                                    </span>
                                </div>

                                <div
                                    class="flex justify-between items-center text-sm text-gray-500"
                                >
                                    <span>{{ formatDate(task.due_date) }}</span>
                                    <div class="flex -space-x-2">
                                        <div
                                            v-for="user in task.assigned_users.slice(
                                                0,
                                                3
                                            )"
                                            :key="user.id"
                                            class="w-6 h-6 rounded-full bg-gray-700 border border-gray-800 flex items-center justify-center text-xs text-gray-300"
                                            :title="user.name"
                                        >
                                            {{ user.name[0] }}
                                        </div>
                                        <div
                                            v-if="
                                                task.assigned_users.length > 3
                                            "
                                            class="w-6 h-6 rounded-full bg-gray-700 border border-gray-800 flex items-center justify-center text-xs text-gray-300"
                                        >
                                            +{{
                                                task.assigned_users.length - 3
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import draggable from "vuedraggable";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["status-change", "view-change", "task-click"]);

const columns = computed(() => [
    {
        status: "pending",
        title: "To Do",
        tasks: props.tasks.filter((task) => task.status === "pending"),
    },
    {
        status: "in-progress",
        title: "In Progress",
        tasks: props.tasks.filter((task) => task.status === "in-progress"),
    },
    {
        status: "completed",
        title: "Completed",
        tasks: props.tasks.filter((task) => task.status === "completed"),
    },
]);

const handleDragChange = (event, newStatus) => {
    if (event.added) {
        const task = event.added.element;
        emit("status-change", { taskId: task.id, status: newStatus });
    }
};

const getPriorityClass = (priority) => {
    const classes = {
        0: "bg-blue-400 text-black",
        1: "bg-yellow-400 text-black",
        2: "bg-red-400 text-black",
    };
    return classes[priority] || "bg-gray-400 text-black";
};

const getPriorityLabel = (priority) => {
    const labels = {
        0: "Low",
        1: "Medium",
        2: "High",
    };
    return labels[priority] || "Unknown";
};

const formatDate = (date) => {
    if (!date) return "No due date";
    return new Date(date).toLocaleDateString();
};
</script>
