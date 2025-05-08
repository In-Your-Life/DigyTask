<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_tag_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user);
        Livewire::test('tag-manager', ['task' => $task])
            ->set('newTag', 'Urgente')
            ->call('addTag')
            ->assertSee('Urgente');
    }

    // Altri test: rimozione tag, filtro...
} 