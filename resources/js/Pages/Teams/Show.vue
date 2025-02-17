<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager.vue";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm.vue";

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Team Settings">
        <template #header>
            <h2
                class="font-semibold text-xl text-yellow-400 leading-tight glitch-text"
            >
                Team Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <UpdateTeamNameForm
                        :team="team"
                        :permissions="permissions"
                    />
                    <TeamMemberManager
                        :team="team"
                        :available-roles="availableRoles"
                        :user-permissions="permissions"
                    />
                    <template
                        v-if="permissions.canDeleteTeam && !team.personal_team"
                    >
                        <DeleteTeamForm :team="team" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
