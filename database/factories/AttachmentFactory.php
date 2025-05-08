<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'filepath' => 'attachments/' . $this->faker->uuid . '.jpg',
            'file_type' => $this->faker->randomElement(['image/jpeg', 'application/pdf', 'image/png']),
            'alt_text' => $this->faker->optional()->sentence(3),
        ];
    }
}
