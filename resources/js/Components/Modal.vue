<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";

defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const closeModal = () => {
    emit("close");
};
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="div">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/75" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-2xl transform overflow-hidden rounded-lg bg-gray-900 p-6 text-left align-middle shadow-xl transition-all border border-yellow-400"
                        >
                            <DialogTitle
                                as="h3"
                                class="text-xl font-bold leading-6 text-yellow-400 mb-4"
                            >
                                {{ title }}
                            </DialogTitle>

                            <div class="mt-2">
                                <slot />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
