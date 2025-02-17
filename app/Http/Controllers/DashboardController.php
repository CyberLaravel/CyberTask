<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskActivity;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get tasks statistics
        $stats = [
            [
                'title' => 'Total Tasks',
                'value' => Task::where('user_id', $user->id)->count(),
                'icon' => 'DocumentIcon',
                'color' => 'blue',
                'route' => 'tasks.index',
            ],
            [
                'title' => 'Pending Tasks',
                'value' => Task::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'icon' => 'ClockIcon',
                'color' => 'yellow',
                'route' => 'tasks.index',
            ],
            [
                'title' => 'Completed Tasks',
                'value' => Task::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'icon' => 'CheckCircleIcon',
                'color' => 'green',
                'route' => 'tasks.index',
            ],
            [
                'title' => 'High Priority',
                'value' => Task::where('user_id', $user->id)
                    ->where('priority', 2)
                    ->count(),
                'icon' => 'ExclamationCircleIcon',
                'color' => 'red',
                'route' => 'tasks.index',
            ],
        ];

        // Get recent activities
        $recentActivity = TaskActivity::with(['task', 'user'])
            ->whereHas('task', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('assignedUsers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'description' => $activity->description,
                    'time' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity->action),
                ];
            });

        // Get active projects count
        $activeProjects = Project::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'activeProjects' => $activeProjects,
        ]);
    }

    private function getActivityIcon($action)
    {
        return match ($action) {
            'created' => 'DocumentPlusIcon',
            'updated' => 'PencilSquareIcon',
            'deleted' => 'TrashIcon',
            'completed' => 'CheckCircleIcon',
            'assigned' => 'UserPlusIcon',
            'unassigned' => 'UserMinusIcon',
            'comment_added' => 'ChatBubbleLeftIcon',
            'attachment_added' => 'PaperClipIcon',
            'dependency_added' => 'LinkIcon',
            'dependency_removed' => 'NoSymbolIcon',
            default => 'InformationCircleIcon',
        };
    }
}
