<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    ChartBarIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    UserGroupIcon,
    DocumentIcon,
    DocumentPlusIcon,
    PencilSquareIcon,
    TrashIcon,
    UserPlusIcon,
    UserMinusIcon,
    ChatBubbleLeftIcon,
    PaperClipIcon,
    LinkIcon,
    NoSymbolIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    stats: {
        type: Array,
        required: true,
    },
    recentActivity: {
        type: Array,
        required: true,
    },
    activeProjects: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
                >
                    <Link
                        v-for="stat in stats"
                        :key="stat.title"
                        :href="route(stat.route)"
                        class="bg-gray-800 border border-yellow-400 rounded-lg p-6 hover:shadow-neon transition-all duration-300 group"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-300 text-sm mb-1">
                                    {{ stat.title }}
                                </p>
                                <p class="text-3xl font-bold text-yellow-400">
                                    {{ stat.value }}
                                </p>
                            </div>
                            <component
                                :is="stat.icon"
                                class="w-12 h-12 text-gray-600 group-hover:text-yellow-400 transition-colors"
                            />
                        </div>
                    </Link>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Activity -->
                    <div
                        class="bg-gray-800 border border-yellow-400 rounded-lg p-6"
                    >
                        <h2
                            class="text-2xl font-bold text-yellow-400 mb-6 glitch-text"
                        >
                            Recent Activity
                        </h2>
                        <div class="space-y-4">
                            <div
                                v-for="(activity, index) in recentActivity"
                                :key="index"
                                class="flex items-start space-x-4 p-4 bg-gray-900 rounded-lg border border-yellow-400"
                            >
                                <div class="flex-shrink-0">
                                    <component
                                        :is="activity.icon"
                                        class="w-6 h-6 text-blue-300"
                                    />
                                </div>
                                <div>
                                    <p class="text-gray-100">
                                        {{ activity.description }}
                                    </p>
                                    <p class="text-sm text-blue-300">
                                        {{ activity.time }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="!recentActivity.length"
                                class="text-gray-500 text-center py-4"
                            >
                                No recent activity
                            </div>
                        </div>
                    </div>

                    <!-- Team Overview -->
                    <div
                        class="bg-gray-800 border border-yellow-400 rounded-lg p-6"
                    >
                        <h2
                            class="text-2xl font-bold text-yellow-400 mb-6 glitch-text"
                        >
                            Team Overview
                        </h2>
                        <div class="space-y-6">
                            <!-- Team Stats -->
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-gray-900 rounded-lg p-4 border border-yellow-400"
                                >
                                    <p class="text-blue-300 text-sm">
                                        Team Members
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-yellow-400"
                                    >
                                        {{
                                            $page.props.auth.user.current_team
                                                ?.team_members?.length || 0
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="bg-gray-900 rounded-lg p-4 border border-yellow-400"
                                >
                                    <p class="text-blue-300 text-sm">
                                        Active Projects
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-yellow-400"
                                    >
                                        {{ activeProjects }}
                                    </p>
                                </div>
                            </div>

                            <!-- Team Members List -->
                            <div class="space-y-3">
                                <div
                                    v-for="member in $page.props.auth.user
                                        .current_team?.team_members || []"
                                    :key="member.id"
                                    class="flex items-center justify-between p-3 bg-gray-900 rounded-lg border border-yellow-400"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-700 border border-yellow-400 flex items-center justify-center"
                                        >
                                            <span class="text-yellow-400">{{
                                                member.name[0]
                                            }}</span>
                                        </div>
                                        <div>
                                            <p class="text-gray-100">
                                                {{ member.name }}
                                            </p>
                                            <p class="text-sm text-blue-300">
                                                {{ member.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.shadow-neon {
    box-shadow: 0 0 5px theme("colors.yellow.400"),
        0 0 20px theme("colors.yellow.400");
}

.glitch-text {
    text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
        -0.025em -0.05em 0 rgba(0, 255, 0, 0.75),
        0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    animation: glitch 500ms infinite;
}

@keyframes glitch {
    0% {
        text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
            -0.05em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    }
    14% {
        text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
            -0.05em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
    }
    15% {
        text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    49% {
        text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    50% {
        text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
            0.05em 0 0 rgba(0, 255, 0, 0.75), 0 -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    99% {
        text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
            0.05em 0 0 rgba(0, 255, 0, 0.75), 0 -0.05em 0 rgba(0, 0, 255, 0.75);
    }
    100% {
        text-shadow: -0.025em 0 0 rgba(255, 0, 0, 0.75),
            -0.025em -0.025em 0 rgba(0, 255, 0, 0.75),
            -0.025em -0.05em 0 rgba(0, 0, 255, 0.75);
    }
}
</style>
