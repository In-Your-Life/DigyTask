<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'is_page' => false,
            'slug' => $this->faker->unique()->slug,
            'status' => $this->faker->randomElement(['draft', 'pending', 'in_progress', 'review', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'deadline' => $this->faker->optional()->dateTimeBetween('now', '+2 months'),
            'created_by' => User::factory(),
            'is_template' => $this->faker->boolean(10),
            'figma_url' => $this->faker->optional()->url,
            'notion_url' => $this->faker->optional()->url,
            'github_url' => $this->faker->optional()->url,
            'slack_channel' => $this->faker->optional()->word,
            'webhook_url' => $this->faker->optional()->url,
        ];
    }
}
