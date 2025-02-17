<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Task;
use App\Models\Project;
use App\Events\TaskUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use App\Models\User;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $tasks = Task::with([
            'user',
            'project',
            'tags',
            'assignedUsers',
            'comments.user',
            'attachments',
            'dependencies',
            'dependents',
            'activities.user'
        ])
            ->where('user_id', Auth::id())
            ->orWhereHas('assignedUsers', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->latest()
            ->get();

        $tags = Tag::where('user_id', Auth::id())->get();
        $users = User::where('id', '!=', Auth::id())->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $users = User::where('id', '!=', Auth::id())->get();

        return Inertia::render('Tasks/Create', [
            'projects' => $projects,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|integer',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'estimated_hours' => 'nullable|numeric',
            'assigned_users' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $task = Task::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        if (!empty($validated['assigned_users'])) {
            $task->assignedUsers()->attach($validated['assigned_users']);
        }

        if (!empty($validated['tags'])) {
            $task->tags()->attach($validated['tags']);
        }

        broadcast(new TaskUpdated($task))->toOthers();

        return redirect()->back()->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load([
            'user',
            'project',
            'tags',
            'assignedUsers',
            'comments.user',
            'attachments',
            'dependencies',
            'dependents',
            'activities.user'
        ]);

        $allTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->get();

        return Inertia::render('Tasks/Show', [
            'task' => $task,
            'allTasks' => $allTasks,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $task->load(['tags', 'assignedUsers']);
        $projects = Project::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $users = User::where('id', '!=', Auth::id())->get();

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'projects' => $projects,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|string',
            'priority' => 'sometimes|required|integer',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric',
            'actual_hours' => 'nullable|numeric',
            'assigned_users' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $task->update($validated);

        if ($request->has('assigned_users')) {
            $task->assignedUsers()->sync($validated['assigned_users']);
        }

        if ($request->has('tags')) {
            $task->tags()->sync($validated['tags']);
        }

        $task->activities()->create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Task was updated',
            'changes' => $task->getChanges(),
        ]);

        broadcast(new TaskUpdated($task))->toOthers();

        return redirect()->back()->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully');
    }

    public function addDependency(Request $request, Task $task)
    {
        $this->authorize('addDependency', $task);

        $validated = $request->validate([
            'dependency_id' => 'required|exists:tasks,id'
        ]);

        // Prevent circular dependencies
        if ($validated['dependency_id'] === $task->id) {
            return redirect()->back()->with('error', 'Task cannot depend on itself');
        }

        // Prevent circular dependencies through longer chains
        $dependencyTask = Task::find($validated['dependency_id']);
        if ($dependencyTask->dependencies()->pluck('id')->contains($task->id)) {
            return redirect()->back()->with('error', 'Circular dependency detected');
        }

        $task->dependencies()->attach($validated['dependency_id']);

        $task->activities()->create([
            'user_id' => Auth::id(),
            'action' => 'dependency_added',
            'description' => 'Task dependency was added',
            'changes' => ['dependency_id' => $validated['dependency_id']]
        ]);

        return redirect()->back()->with('success', 'Dependency added successfully');
    }

    public function removeDependency(Task $task, Task $dependency)
    {
        $this->authorize('removeDependency', $task);

        $task->dependencies()->detach($dependency->id);

        $task->activities()->create([
            'user_id' => Auth::id(),
            'action' => 'dependency_removed',
            'description' => 'Task dependency was removed',
            'changes' => ['removed_dependency_id' => $dependency->id]
        ]);

        return redirect()->back()->with('success', 'Dependency removed successfully');
    }
}
