<?php

namespace Database\Factories;

use Database\Seeders\ProjectSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // if no project exists, call project seeder
        if (\App\Models\Project::count() === 0) {
            Artisan::call('db:seed', ['--class' => ProjectSeeder::class]);
        }
        return [
            'name' => $this->faker->sentence,
            // pick a random project and assign ID to project_id
            'project_id' => \App\Models\Project::inRandomOrder()->first()->id,

        ];
    }
}
