<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CommentSection from "@/Components/Tasks/CommentSection.vue";
import AttachmentSection from "@/Components/Tasks/AttachmentSection.vue";
import DependencySection from "@/Components/Tasks/DependencySection.vue";
import ActivityTimeline from "@/Components/Tasks/ActivityTimeline.vue";

defineProps({
    task: {
        type: Object,
        required: true,
    },
    allTasks: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout :title="task.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-yellow-400 leading-tight">
                    {{ task.title }}
                </h2>
                <Link
                    :href="route('tasks.edit', task.id)"
                    class="px-4 py-2 bg-yellow-400 text-black rounded-md hover:bg-yellow-300 transition-colors duration-200"
                >
                    Edit Task
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
                >
                    <p class="text-gray-100">{{ task.description }}</p>
                </div>

                <DependencySection
                    :task-id="task.id"
                    :dependencies="task.dependencies"
                    :dependents="task.dependents"
                    :all-tasks="allTasks"
                />

                <CommentSection
                    :task-id="task.id"
                    :comments="task.comments"
                    :user-id="$page.props.auth.user.id"
                />

                <AttachmentSection
                    :task-id="task.id"
                    :attachments="task.attachments"
                    :user-id="$page.props.auth.user.id"
                />

                <ActivityTimeline :activities="task.activities" />
            </div>
        </div>
    </AppLayout>
</template>
