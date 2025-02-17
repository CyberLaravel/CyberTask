<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
    >
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-yellow-400 glitch-text">
                Projects
            </h2>
            <button
                @click="showCreateModal = true"
                class="px-4 py-2 bg-yellow-400 text-black rounded-md hover:bg-yellow-300 transition-colors duration-200"
            >
                New Project
            </button>
        </div>

        <!-- Projects Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="project in projects"
                :key="project.id"
                class="bg-gray-900 rounded-lg p-6 border border-yellow-400 hover:shadow-neon transition-all duration-300"
            >
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-blue-300">
                        {{ project.name }}
                    </h3>
                    <span
                        class="px-2 py-1 text-xs rounded-full"
                        :class="getStatusClass(project.status)"
                    >
                        {{ project.status }}
                    </span>
                </div>

                <p class="text-gray-400 mb-4">{{ project.description }}</p>

                <!-- Project Stats -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Tasks</span>
                        <span class="text-yellow-400">{{
                            project.tasks_count
                        }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Completed</span>
                        <span class="text-green-400">
                            {{ project.completed_tasks_count }}
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
                    <div
                        class="bg-yellow-400 h-full rounded-full transition-all duration-500"
                        :style="{
                            width: `${
                                (project.completed_tasks_count /
                                    project.tasks_count) *
                                100
                            }%`,
                        }"
                    ></div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-2">
                    <button
                        @click="editProject(project)"
                        class="text-blue-300 hover:text-yellow-400 transition-colors"
                    >
                        Edit
                    </button>
                    <button
                        @click="deleteProject(project)"
                        class="text-blue-300 hover:text-yellow-400 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal
            :is-open="showCreateModal"
            :title="editingProject ? 'Edit Project' : 'Create Project'"
            @close="closeModal"
        >
            <form @submit.prevent="submitProject" class="space-y-4">
                <div>
                    <label class="block text-yellow-400 mb-2">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    />
                </div>
                <div>
                    <label class="block text-yellow-400 mb-2"
                        >Description</label
                    >
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    ></textarea>
                </div>
                <div>
                    <label class="block text-yellow-400 mb-2">Status</label>
                    <select
                        v-model="form.status"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option value="active">Active</option>
                        <option value="on-hold">On Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 border border-yellow-400 text-yellow-400 rounded-md hover:bg-yellow-400 hover:text-black transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-yellow-400 text-black rounded-md hover:bg-yellow-300 transition-colors duration-200"
                    >
                        {{ editingProject ? "Update" : "Create" }}
                    </button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const showCreateModal = ref(false);
const editingProject = ref(null);

const form = useForm({
    name: "",
    description: "",
    status: "active",
});

const submitProject = () => {
    if (editingProject.value) {
        form.put(route("projects.update", editingProject.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("projects.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const editProject = (project) => {
    editingProject.value = project;
    form.name = project.name;
    form.description = project.description;
    form.status = project.status;
    showCreateModal.value = true;
};

const deleteProject = (project) => {
    if (confirm("Are you sure you want to delete this project?")) {
        router.delete(route("projects.destroy", project.id));
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    editingProject.value = null;
    form.reset();
};

const getStatusClass = (status) => {
    const classes = {
        active: "bg-green-400 text-black",
        "on-hold": "bg-yellow-400 text-black",
        completed: "bg-blue-400 text-black",
    };
    return classes[status] || "bg-gray-400 text-black";
};
</script>
