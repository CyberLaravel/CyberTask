<template>
    <AppLayout title="Tasks">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="border-b border-yellow-400 pb-4 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-yellow-400 glitch-text">
                        Task Manager
                    </h1>
                    <div class="flex space-x-4">
                        <button
                            @click="viewMode = 'list'"
                            class="px-4 py-2 rounded-md transition-colors duration-200"
                            :class="
                                viewMode === 'list'
                                    ? 'bg-yellow-400 text-black'
                                    : 'border border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black'
                            "
                        >
                            List View
                        </button>
                        <button
                            @click="viewMode = 'kanban'"
                            class="px-4 py-2 rounded-md transition-colors duration-200"
                            :class="
                                viewMode === 'kanban'
                                    ? 'bg-yellow-400 text-black'
                                    : 'border border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black'
                            "
                        >
                            Kanban View
                        </button>
                    </div>
                </div>
            </div>

            <TaskDashboard :tasks="filteredTasks" class="mb-8" />

            <TaskFilters :available-tags="tags" v-model:filters="filters" />

            <!-- Task Creation Form -->
            <TaskForm
                :available-tags="tags"
                submit-label="Create Task"
                @submit="handleCreateTask"
            />

            <!-- Task Views -->
            <KanbanBoard
                v-if="viewMode === 'kanban'"
                :tasks="filteredTasks"
                @status-change="handleStatusChange"
                @view-change="viewMode = $event"
                @task-click="showTaskDetails"
            />
            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="task in filteredTasks"
                    :key="task.id"
                    class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon hover:shadow-neon-lg transition-all duration-300"
                    @click="showTaskDetails(task)"
                >
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-blue-300">
                            {{ task.title }}
                        </h3>
                        <span
                            class="px-2 py-1 text-xs rounded-full"
                            :class="getStatusClass(task.status)"
                        >
                            {{ task.status }}
                        </span>
                    </div>

                    <div class="text-gray-100 mb-4">{{ task.description }}</div>

                    <div class="flex flex-wrap gap-2 mb-4">
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
                        class="flex justify-between items-center text-gray-400 text-sm"
                    >
                        <div>Due: {{ formatDate(task.due_date) }}</div>
                        <div class="flex items-center space-x-2">
                            <button
                                @click="editTask(task)"
                                class="text-blue-300 hover:text-yellow-400 transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteTask(task)"
                                class="text-blue-300 hover:text-yellow-400 transition-colors"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal
            :is-open="isEditModalOpen"
            title="Edit Task"
            @close="closeEditModal"
        >
            <TaskForm
                v-if="editingTask"
                :task="editingTask"
                :available-tags="tags"
                submit-label="Update Task"
                :show-cancel="true"
                @submit="handleEditTask"
                @cancel="closeEditModal"
            />
        </Modal>

        <Modal
            :is-open="!!selectedTask"
            :title="selectedTask?.title ?? ''"
            @close="selectedTask = null"
        >
            <div v-if="selectedTask" class="space-y-6">
                <div class="text-gray-100">{{ selectedTask.description }}</div>

                <TeamAssignment
                    :task-id="selectedTask.id"
                    :assigned-users="selectedTask.assigned_users"
                    :all-users="users"
                    :can-manage-assignments="true"
                />

                <DependencySection
                    :task-id="selectedTask.id"
                    :dependencies="selectedTask.dependencies"
                    :dependents="selectedTask.dependents"
                    :all-tasks="tasks"
                />

                <CommentSection
                    :task-id="selectedTask.id"
                    :comments="selectedTask.comments"
                    :user-id="$page.props.auth.user.id"
                />

                <AttachmentSection
                    :task-id="selectedTask.id"
                    :attachments="selectedTask.attachments"
                    :user-id="$page.props.auth.user.id"
                />

                <ActivityTimeline :activities="selectedTask.activities" />
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import TaskForm from "@/Components/Tasks/TaskForm.vue";
import Modal from "@/Components/Modal.vue";
import CommentSection from "@/Components/Tasks/CommentSection.vue";
import AttachmentSection from "@/Components/Tasks/AttachmentSection.vue";
import DependencySection from "@/Components/Tasks/DependencySection.vue";
import ActivityTimeline from "@/Components/Tasks/ActivityTimeline.vue";
import TaskFilters from "@/Components/Tasks/TaskFilters.vue";
import TaskDashboard from "@/Components/Tasks/TaskDashboard.vue";
import KanbanBoard from "@/Components/Tasks/KanbanBoard.vue";
import TeamAssignment from "@/Components/Tasks/TeamAssignment.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
    tags: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
});

const editingTask = ref(null);
const isEditModalOpen = ref(false);
const selectedTask = ref(null);
const viewMode = ref("list"); // 'list' or 'kanban'

const filters = ref({
    search: "",
    status: "",
    priority: "",
    tags: [],
});

const filteredTasks = computed(() => {
    return props.tasks.filter((task) => {
        // Search filter
        if (
            filters.value.search &&
            !task.title
                .toLowerCase()
                .includes(filters.value.search.toLowerCase())
        ) {
            return false;
        }

        // Status filter
        if (filters.value.status && task.status !== filters.value.status) {
            return false;
        }

        // Priority filter
        if (
            filters.value.priority !== "" &&
            task.priority !== parseInt(filters.value.priority)
        ) {
            return false;
        }

        // Tags filter
        if (filters.value.tags.length > 0) {
            const taskTagIds = task.tags.map((t) => t.id);
            if (
                !filters.value.tags.every((tagId) => taskTagIds.includes(tagId))
            ) {
                return false;
            }
        }

        return true;
    });
});

const handleCreateTask = (form) => {
    form.post(route("tasks.store"), {
        onSuccess: () => form.reset(),
    });
};

const handleEditTask = (form) => {
    form.put(route("tasks.update", editingTask.value.id), {
        onSuccess: () => {
            isEditModalOpen.value = false;
            editingTask.value = null;
        },
    });
};

const openEditModal = (task) => {
    editingTask.value = task;
    isEditModalOpen.value = true;
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editingTask.value = null;
};

const getStatusClass = (status) => {
    const classes = {
        pending: "bg-yellow-400 text-black",
        "in-progress": "bg-blue-400 text-black",
        completed: "bg-green-400 text-black",
    };
    return classes[status] || "bg-gray-400 text-black";
};

const formatDate = (date) => {
    if (!date) return "No due date";
    return new Date(date).toLocaleDateString();
};

const showTaskDetails = (task) => {
    selectedTask.value = task;
};

const handleStatusChange = ({ taskId, status }) => {
    router.put(
        route("tasks.update", taskId),
        {
            status: status,
        },
        {
            preserveScroll: true,
        }
    );
};
</script>

<style>
.shadow-neon {
    box-shadow: 0 0 5px theme("colors.yellow.400"),
        0 0 10px theme("colors.yellow.400");
}

.shadow-neon-lg {
    box-shadow: 0 0 10px theme("colors.yellow.400"),
        0 0 20px theme("colors.yellow.400"), 0 0 30px theme("colors.yellow.400");
}

.glitch-text {
    text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
        -0.025em -0.05em 0 rgba(0, 255, 0, 0.75),
        0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    animation: glitch 500ms infinite;
}

@keyframes glitch {
    0% {
        text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
            -0.05em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    }
    14% {
        text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
            -0.05em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    }
    15% {
        text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    49% {
        text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    50% {
        text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
            0.05em 0 0 rgba(0, 255, 0, 0.75), 0 -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    99% {
        text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
            0.05em 0 0 rgba(0, 255, 0, 0.75), 0 -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    100% {
        text-shadow: -0.025em 0 0 rgba(255, 0, 0, 0.75),
            -0.025em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
}
</style>
