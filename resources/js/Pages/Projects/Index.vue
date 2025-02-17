<template>
    <AppLayout title="Projects">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <ProjectList :projects="projects" />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectList from "@/Components/Projects/ProjectList.vue";

defineProps({
    projects: {
        type: Array,
        required: true,
    },
});
</script>
