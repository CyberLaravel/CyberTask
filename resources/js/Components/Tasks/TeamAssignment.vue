<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mt-6"
    >
        <h3 class="text-xl font-bold text-yellow-400 mb-4">Team Assignment</h3>

        <!-- Team Members List -->
        <div class="space-y-4">
            <div class="flex flex-wrap gap-3">
                <div
                    v-for="user in assignedUsers"
                    :key="user.id"
                    class="group relative bg-gray-900 rounded-lg p-3 border border-yellow-400"
                >
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gray-700 border border-yellow-400 flex items-center justify-center text-lg text-yellow-400"
                        >
                            {{ user.name[0] }}
                        </div>
                        <div>
                            <div class="text-gray-100 font-semibold">
                                {{ user.name }}
                            </div>
                            <div class="text-gray-400 text-sm">
                                {{ user.email }}
                            </div>
                        </div>
                        <button
                            v-if="canManageAssignments"
                            @click="removeUser(user)"
                            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-300 transition-opacity"
                        >
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add Team Member -->
            <div v-if="canManageAssignments" class="mt-6">
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <select
                            v-model="selectedUser"
                            class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                        >
                            <option value="">Select team member...</option>
                            <option
                                v-for="user in availableUsers"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                    </div>
                    <button
                        @click="addUser"
                        :disabled="!selectedUser"
                        class="px-4 py-2 bg-yellow-400 text-black rounded-md hover:bg-yellow-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Add Member
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    taskId: {
        type: Number,
        required: true,
    },
    assignedUsers: {
        type: Array,
        required: true,
    },
    allUsers: {
        type: Array,
        required: true,
    },
    canManageAssignments: {
        type: Boolean,
        default: false,
    },
});

const selectedUser = ref("");

const availableUsers = computed(() => {
    return props.allUsers.filter(
        (user) =>
            !props.assignedUsers.find((assigned) => assigned.id === user.id)
    );
});

const addUser = () => {
    if (selectedUser.value) {
        router.post(
            route("tasks.assignments.store", props.taskId),
            { user_id: selectedUser.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    selectedUser.value = "";
                },
            }
        );
    }
};

const removeUser = (user) => {
    router.delete(route("tasks.assignments.destroy", [props.taskId, user.id]), {
        preserveScroll: true,
    });
};
</script>
