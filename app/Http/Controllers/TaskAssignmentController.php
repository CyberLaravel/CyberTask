<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $task->assignedUsers()->attach($validated['user_id']);

        $task->activities()->create([
            'user_id' => auth()->id(),
            'action' => 'assigned',
            'description' => 'User was assigned to task',
            'changes' => ['assigned_user_id' => $validated['user_id']]
        ]);

        return redirect()->back()->with('success', 'User assigned successfully');
    }

    public function destroy(Task $task, User $user)
    {
        $task->assignedUsers()->detach($user->id);

        $task->activities()->create([
            'user_id' => auth()->id(),
            'action' => 'unassigned',
            'description' => 'User was unassigned from task',
            'changes' => ['unassigned_user_id' => $user->id]
        ]);

        return redirect()->back()->with('success', 'User unassigned successfully');
    }
}
