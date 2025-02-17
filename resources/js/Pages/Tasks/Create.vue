<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import TaskForm from "@/Components/Tasks/TaskForm.vue";

const props = defineProps({
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
    title: "",
    description: "",
    status: "pending",
    priority: 0,
    due_date: null,
    project_id: null,
    estimated_hours: null,
    assigned_users: [],
    tags: [],
});

const handleSubmit = () => {
    form.post(route("tasks.store"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Create Task">
        <template #header>
            <h2 class="font-semibold text-xl text-yellow-400 leading-tight">
                Create New Task
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <TaskForm
                    v-model:title="form.title"
                    v-model:description="form.description"
                    v-model:status="form.status"
                    v-model:priority="form.priority"
                    v-model:due_date="form.due_date"
                    v-model:project_id="form.project_id"
                    v-model:estimated_hours="form.estimated_hours"
                    v-model:assigned_users="form.assigned_users"
                    v-model:tags="form.tags"
                    :available-tags="tags"
                    :available-projects="projects"
                    :available-users="users"
                    submit-label="Create Task"
                    @submit="handleSubmit"
                    :errors="form.errors"
                    :disabled="form.processing"
                />
            </div>
        </div>
    </AppLayout>
</template>
