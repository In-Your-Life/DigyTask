<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('tasks.show', $task));
        $response->assertStatus(200);
    }

    // Altri test: update, delete, share, comment...
} 