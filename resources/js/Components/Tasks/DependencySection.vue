<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mt-6"
    >
        <h3 class="text-xl font-bold text-yellow-400 mb-4">Dependencies</h3>

        <!-- Dependencies List -->
        <div class="mb-6">
            <h4 class="text-blue-300 mb-2">This task depends on:</h4>
            <div class="space-y-2">
                <div
                    v-for="dependency in dependencies"
                    :key="dependency.id"
                    class="flex items-center justify-between bg-gray-900 p-3 rounded-md border border-yellow-400"
                >
                    <span class="text-gray-100">{{ dependency.title }}</span>
                    <button
                        @click="removeDependency(dependency)"
                        class="text-red-400 hover:text-red-300 transition-colors"
                    >
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
                <div v-if="!dependencies.length" class="text-gray-400 italic">
                    No dependencies
                </div>
            </div>
        </div>

        <!-- Dependents List -->
        <div class="mb-6">
            <h4 class="text-blue-300 mb-2">Tasks that depend on this:</h4>
            <div class="space-y-2">
                <div
                    v-for="dependent in dependents"
                    :key="dependent.id"
                    class="bg-gray-900 p-3 rounded-md border border-yellow-400"
                >
                    <span class="text-gray-100">{{ dependent.title }}</span>
                </div>
                <div v-if="!dependents.length" class="text-gray-400 italic">
                    No dependent tasks
                </div>
            </div>
        </div>

        <!-- Add Dependency -->
        <div class="mt-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <select
                        v-model="selectedTask"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option value="">
                            Select task to add as dependency...
                        </option>
                        <option
                            v-for="task in availableTasks"
                            :key="task.id"
                            :value="task.id"
                        >
                            {{ task.title }}
                        </option>
                    </select>
                </div>
                <button
                    @click="addDependency"
                    :disabled="!selectedTask"
                    class="px-4 py-2 bg-yellow-400 text-black rounded-md hover:bg-yellow-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Add Dependency
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    taskId: {
        type: Number,
        required: true,
    },
    dependencies: {
        type: Array,
        required: true,
    },
    dependents: {
        type: Array,
        required: true,
    },
    allTasks: {
        type: Array,
        required: true,
    },
});

console.log({
    dependencies: props.dependencies,
    dependents: props.dependents,
    allTasks: props.allTasks,
});

const selectedTask = ref("");

const availableTasks = computed(() => {
    return props.allTasks.filter(
        (task) =>
            task.id !== props.taskId &&
            !props.dependencies.find((dep) => dep.id === task.id)
    );
});

const addDependency = () => {
    if (selectedTask.value) {
        router.post(
            route("tasks.dependencies.store", props.taskId),
            {
                dependency_id: selectedTask.value,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    selectedTask.value = "";
                },
            }
        );
    }
};

const removeDependency = (dependency) => {
    router.delete(
        route("tasks.dependencies.destroy", [props.taskId, dependency.id]),
        {
            preserveScroll: true,
        }
    );
};
</script>
