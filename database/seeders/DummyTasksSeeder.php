<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\Activity;
use App\Models\TaskShare;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DummyTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $roles = Role::all();
        $tags = Tag::factory(15)->create();
        Task::factory(30)->create()->each(function ($task) use ($users, $roles, $tags) {
            // Tag
            $task->tags()->sync($tags->random(rand(1,3))->pluck('id'));
            // Ruoli
            $task->roles()->sync($roles->random(rand(1,2))->pluck('id'));
            // Assegnati (popola task_role con tutte le chiavi)
            $assignedUsers = $users->random(rand(1,2));
            $taskRoles = $roles->random(rand(1,2));
            foreach ($assignedUsers as $user) {
                foreach ($taskRoles as $role) {
                    DB::table('task_role')->insert([
                        'task_id' => $task->id,
                        'role_id' => $role->id,
                        'assigned_user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // Commenti
            Comment::factory(rand(2,5))->create(['task_id' => $task->id, 'user_id' => $users->random()->id]);
            // Allegati
            Attachment::factory(rand(1,3))->create(['task_id' => $task->id, 'user_id' => $users->random()->id]);
            // Attività
            Activity::create([
                'task_id' => $task->id,
                'user_id' => $users->random()->id,
                'description' => 'Task creato',
            ]);
            // Condivisione pubblica
            if (rand(0,1)) {
                TaskShare::create([
                    'task_id' => $task->id,
                    'token' => Str::uuid()->toString(),
                    'created_by' => $users->random()->id,
                    'is_active' => true,
                ]);
            }
        });
    }
}
