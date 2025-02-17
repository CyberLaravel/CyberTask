<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mt-6"
    >
        <h3 class="text-xl font-bold text-yellow-400 mb-4">Attachments</h3>

        <!-- Upload Form -->
        <form @submit.prevent="submitFile" class="mb-6">
            <div class="flex items-center space-x-4">
                <input
                    type="file"
                    ref="fileInput"
                    @change="handleFileChange"
                    class="hidden"
                />
                <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="px-4 py-2 border border-yellow-400 text-yellow-400 rounded-md hover:bg-yellow-400 hover:text-black transition-colors duration-200"
                >
                    Select File
                </button>
                <span v-if="selectedFile" class="text-gray-300">
                    {{ selectedFile.name }}
                </span>
                <button
                    v-if="selectedFile"
                    type="submit"
                    class="bg-yellow-400 text-black px-4 py-2 rounded-md hover:bg-yellow-300 transition-colors duration-200"
                    :disabled="form.processing"
                >
                    Upload
                </button>
            </div>
            <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">
                {{ form.errors.file }}
            </div>
        </form>

        <!-- Attachments List -->
        <div class="grid gap-4 md:grid-cols-2">
            <div
                v-for="file in attachments"
                :key="file.id"
                class="bg-gray-900 rounded-lg p-4 border border-yellow-400"
            >
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <DocumentIcon class="w-5 h-5 text-yellow-400" />
                        <span class="text-gray-100">{{ file.name }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="downloadFile(file)"
                            class="text-blue-300 hover:text-yellow-400 transition-colors"
                        >
                            Download
                        </button>
                        <button
                            v-if="canDeleteFile(file)"
                            @click="deleteFile(file)"
                            class="text-blue-300 hover:text-yellow-400 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>
                <div class="text-gray-500 text-sm mt-1">
                    {{ formatFileSize(file.file_size) }} •
                    {{ formatDate(file.created_at) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DocumentIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    taskId: {
        type: Number,
        required: true,
    },
    attachments: {
        type: Array,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

const fileInput = ref(null);
const selectedFile = ref(null);

const form = useForm({
    file: null,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
        form.file = file;
    }
};

const submitFile = () => {
    form.post(route("tasks.attachments.store", props.taskId), {
        onSuccess: () => {
            form.reset();
            selectedFile.value = null;
            if (fileInput.value) {
                fileInput.value.value = "";
            }
        },
    });
};

const downloadFile = (file) => {
    window.location.href = route("attachments.download", file.id);
};

const deleteFile = (file) => {
    if (confirm("Are you sure you want to delete this file?")) {
        router.delete(route("attachments.destroy", file.id));
    }
};

const canDeleteFile = (file) => {
    return file.user_id === props.userId;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};
</script>
