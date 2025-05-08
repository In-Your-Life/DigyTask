<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Descrizione di test',
        ]);
        $response->assertStatus(302); // redirect dopo creazione
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_user_can_duplicate_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('tasks.duplicate', $task));
        $response->assertStatus(200);
    }

    public function test_user_can_change_task_status()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('tasks.changeStatus', $task), ['status' => 'completed']);
        $response->assertStatus(200);
    }

    public function test_user_can_add_comment_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('comments.store'), [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'Nuovo commento',
        ]);
        $response->assertStatus(302);
    }

    public function test_user_can_add_attachment_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('attachments.store'), [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'filepath' => 'attachments/test.jpg',
            'file_type' => 'image/jpeg',
        ]);
        $response->assertStatus(302);
    }

    // Altri test: assegnazione, cambio stato, commenti, allegati...
} 