<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserPreference;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();
        User::factory(20)->create()->each(function ($user) use ($roles) {
            // Assegna 1-2 ruoli random
            $user->roles()->sync($roles->random(rand(1,2))->pluck('id'));
            // Preferenze demo
            UserPreference::create([
                'user_id' => $user->id,
                'per_page' => rand(10, 30),
                'theme' => rand(0,1) ? 'light' : 'dark',
                'notifications_enabled' => true,
                'email_notifications' => (bool)rand(0,1),
                'granular_notifications' => json_encode([
                    'commenti' => (bool)rand(0,1),
                    'assegnazioni' => (bool)rand(0,1),
                    'stato' => (bool)rand(0,1),
                    'allegati' => (bool)rand(0,1),
                ]),
            ]);
        });
    }
}
