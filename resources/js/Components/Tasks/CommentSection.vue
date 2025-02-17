<template>
    <div
        class="bg-gray-800 rounded-lg p-6 border border-yellow-400 shadow-neon mt-6"
    >
        <h3 class="text-xl font-bold text-yellow-400 mb-4">Comments</h3>

        <!-- Comment Form -->
        <form @submit.prevent="submitComment" class="mb-6">
            <textarea
                v-model="commentContent"
                rows="3"
                placeholder="Write a comment..."
                class="w-full bg-gray-800 border border-yellow-400 text-gray-100 px-4 py-2 rounded-md focus:ring-2 focus:ring-yellow-400 placeholder-gray-500"
                :class="{
                    'border-red-500': editingComment
                        ? editForm.errors.content
                        : form.errors.content,
                }"
            ></textarea>
            <div class="flex justify-end mt-2 space-x-2">
                <button
                    v-if="editingComment"
                    type="button"
                    @click="cancelEdit"
                    class="px-4 py-2 border border-yellow-400 text-yellow-400 rounded-md hover:bg-yellow-400 hover:text-black transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="bg-yellow-400 text-black px-4 py-2 rounded-md hover:bg-yellow-300 transition-colors duration-200"
                    :disabled="
                        editingComment ? editForm.processing : form.processing
                    "
                >
                    {{ editingComment ? "Update Comment" : "Post Comment" }}
                </button>
            </div>
        </form>

        <!-- Comments List -->
        <div class="space-y-4">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="bg-gray-900 rounded-lg p-4 border border-yellow-400"
            >
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <span class="text-yellow-400 font-bold">{{
                            comment.user.name
                        }}</span>
                        <span class="text-gray-500 text-sm ml-2">
                            {{ formatDate(comment.created_at) }}
                        </span>
                    </div>
                    <div
                        v-if="canModifyComment(comment)"
                        class="flex space-x-2"
                    >
                        <button
                            @click="editComment(comment)"
                            class="text-blue-300 hover:text-yellow-400 transition-colors"
                        >
                            Edit
                        </button>
                        <button
                            @click="deleteComment(comment)"
                            class="text-blue-300 hover:text-yellow-400 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>
                <p class="text-gray-100">{{ comment.content }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    taskId: {
        type: Number,
        required: true,
    },
    comments: {
        type: Array,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

const form = useForm({
    content: "",
    task_id: props.taskId,
});

const editingComment = ref(null);
const editForm = useForm({
    content: "",
});

const commentContent = computed({
    get: () => (editingComment.value ? editForm.content : form.content),
    set: (value) =>
        editingComment.value
            ? (editForm.content = value)
            : (form.content = value),
});

const submitComment = () => {
    if (editingComment.value) {
        editForm.put(route("comments.update", editingComment.value.id), {
            onSuccess: () => {
                editForm.reset();
                editingComment.value = null;
            },
        });
    } else {
        form.post(route("tasks.comments.store", props.taskId), {
            onSuccess: () => form.reset(),
        });
    }
};

const canModifyComment = (comment) => {
    return comment.user_id === props.userId;
};

const editComment = (comment) => {
    editingComment.value = comment;
    editForm.content = comment.content;
};

const cancelEdit = () => {
    editingComment.value = null;
    editForm.reset();
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

const deleteComment = (comment) => {
    if (confirm("Are you sure you want to delete this comment?")) {
        router.delete(route("comments.destroy", comment.id));
    }
};
</script>
