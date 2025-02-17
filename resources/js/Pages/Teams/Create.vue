<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
    name: "",
});

const createTeam = () => {
    form.post(route("teams.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2
                class="font-semibold text-xl text-yellow-400 leading-tight glitch-text"
            >
                Create Team
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FormSection @submitted="createTeam">
                    <template #title>
                        <span class="text-yellow-400">Team Details</span>
                    </template>

                    <template #description>
                        <span class="text-blue-300"
                            >Create a new team to collaborate with others.</span
                        >
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Team Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                autofocus
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
