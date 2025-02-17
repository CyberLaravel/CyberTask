<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        // Create some tags for each user
        foreach ($users as $user) {
            Tag::factory(5)->create([
                'user_id' => $user->id,
            ]);
        }

        // Create tasks for each project
        foreach ($projects as $project) {
            $tasks = Task::factory(5)->create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
            ]);

            // Assign random tags to tasks
            foreach ($tasks as $task) {
                $tags = Tag::where('user_id', $task->user_id)
                    ->inRandomOrder()
                    ->take(rand(1, 3))
                    ->get();

                $task->tags()->attach($tags);

                // Assign random users to tasks
                $assignedUsers = User::inRandomOrder()
                    ->take(rand(1, 3))
                    ->get();

                $task->assignedUsers()->attach($assignedUsers);

                // Create some task dependencies
                if (rand(0, 1)) {
                    $dependencyTask = Task::where('project_id', $project->id)
                        ->where('id', '!=', $task->id)
                        ->inRandomOrder()
                        ->first();

                    if ($dependencyTask) {
                        $task->dependencies()->attach($dependencyTask);
                    }
                }
            }
        }
    }
}
