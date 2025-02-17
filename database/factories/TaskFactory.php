<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in-progress', 'completed']),
            'priority' => fake()->randomElement([0, 1, 2]),
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'estimated_hours' => fake()->randomFloat(2, 1, 40),
            'actual_hours' => fake()->randomFloat(2, 1, 40),
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
        ];
    }
}
