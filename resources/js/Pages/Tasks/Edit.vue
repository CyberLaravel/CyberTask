<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import TaskForm from "@/Components/Tasks/TaskForm.vue";

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
    projects: {
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

const form = useForm({
    title: props.task.title,
    description: props.task.description,
    status: props.task.status,
    priority: props.task.priority,
    due_date: props.task.due_date,
    project_id: props.task.project_id,
    estimated_hours: props.task.estimated_hours,
    assigned_users: props.task.assigned_users.map((user) => user.id),
    tags: props.task.tags.map((tag) => tag.id),
});

const handleSubmit = () => {
    form.put(route("tasks.update", props.task.id), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout :title="`Edit Task: ${task.title}`">
        <template #header>
            <h2 class="font-semibold text-xl text-yellow-400 leading-tight">
                Edit Task: {{ task.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <TaskForm
                    :task="task"
                    :available-tags="tags"
                    :available-projects="projects"
                    :available-users="users"
                    submit-label="Update Task"
                    @submit="handleSubmit"
                />
            </div>
        </div>
    </AppLayout>
</template>
