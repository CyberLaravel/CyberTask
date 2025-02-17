<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Title -->
                <div class="col-span-2">
                    <label class="block text-yellow-400 mb-2">Title</label>
                    <input
                        :value="title"
                        @input="$emit('update:title', $event.target.value)"
                        type="text"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                        :class="{ 'border-red-500': errors.title }"
                    />
                    <div v-if="errors.title" class="text-red-500 text-sm mt-1">
                        {{ errors.title }}
                    </div>
                </div>

                <!-- Project -->
                <div class="col-span-2">
                    <label class="block text-yellow-400 mb-2">Project</label>
                    <select
                        :value="project_id"
                        @input="
                            $emit(
                                'update:project_id',
                                Number($event.target.value)
                            )
                        "
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                        :class="{ 'border-red-500': errors.project_id }"
                    >
                        <option value="">Select Project</option>
                        <option
                            v-for="project in availableProjects"
                            :key="project.id"
                            :value="project.id"
                        >
                            {{ project.name }}
                        </option>
                    </select>
                    <div
                        v-if="errors.project_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.project_id }}
                    </div>
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-yellow-400 mb-2"
                        >Description</label
                    >
                    <textarea
                        :value="description"
                        @input="
                            $emit('update:description', $event.target.value)
                        "
                        rows="3"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    ></textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-yellow-400 mb-2">Status</label>
                    <select
                        :value="status"
                        @input="$emit('update:status', $event.target.value)"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-yellow-400 mb-2">Priority</label>
                    <select
                        :value="priority"
                        @input="
                            $emit(
                                'update:priority',
                                Number($event.target.value)
                            )
                        "
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option value="0">Low</option>
                        <option value="1">Medium</option>
                        <option value="2">High</option>
                    </select>
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-yellow-400 mb-2">Due Date</label>
                    <input
                        :value="due_date"
                        @input="$emit('update:due_date', $event.target.value)"
                        type="date"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    />
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label class="block text-yellow-400 mb-2"
                        >Estimated Hours</label
                    >
                    <input
                        :value="estimated_hours"
                        @input="
                            $emit(
                                'update:estimated_hours',
                                Number($event.target.value)
                            )
                        "
                        type="number"
                        step="0.5"
                        min="0"
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    />
                </div>

                <!-- Assigned Users -->
                <div class="col-span-2">
                    <label class="block text-yellow-400 mb-2"
                        >Assigned Users</label
                    >
                    <select
                        :value="assigned_users"
                        @input="
                            $emit(
                                'update:assigned_users',
                                Array.from(
                                    $event.target.selectedOptions,
                                    (option) => Number(option.value)
                                )
                            )
                        "
                        multiple
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option
                            v-for="user in availableUsers"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <!-- Tags -->
                <div class="col-span-2">
                    <label class="block text-yellow-400 mb-2">Tags</label>
                    <select
                        :value="tags"
                        @input="
                            $emit(
                                'update:tags',
                                Array.from(
                                    $event.target.selectedOptions,
                                    (option) => Number(option.value)
                                )
                            )
                        "
                        multiple
                        class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400"
                    >
                        <option
                            v-for="tag in availableTags"
                            :key="tag.id"
                            :value="tag.id"
                        >
                            {{ tag.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <button
                    v-if="showCancel"
                    type="button"
                    @click="$emit('cancel')"
                    class="px-6 py-2 border border-yellow-400 text-yellow-400 rounded-md hover:bg-yellow-400 hover:text-black transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="bg-yellow-400 text-black px-6 py-2 rounded-md hover:bg-yellow-300 transition-colors duration-200"
                    :disabled="disabled"
                >
                    {{ submitLabel }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        required: true,
    },
    priority: {
        type: Number,
        required: true,
    },
    due_date: {
        type: String,
        default: null,
    },
    project_id: {
        type: Number,
        default: null,
    },
    estimated_hours: {
        type: Number,
        default: null,
    },
    assigned_users: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
    task: {
        type: Object,
        default: null,
    },
    availableProjects: {
        type: Array,
        required: true,
    },
    availableTags: {
        type: Array,
        required: true,
    },
    availableUsers: {
        type: Array,
        required: true,
    },
    showCancel: {
        type: Boolean,
        default: false,
    },
    submitLabel: {
        type: String,
        default: "Submit",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits([
    "update:title",
    "update:description",
    "update:status",
    "update:priority",
    "update:due_date",
    "update:project_id",
    "update:estimated_hours",
    "update:assigned_users",
    "update:tags",
    "submit",
    "cancel",
]);

const handleSubmit = () => {
    emit("submit");
};
</script>
