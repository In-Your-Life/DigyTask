<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;

class UserManagement extends Component
{
    public $editingUserId = null;
    public $editingRoles = [];
    public $activeOnly = true;

    public function editRoles($userId)
    {
        $this->editingUserId = $userId;
        $user = User::findOrFail($userId);
        $this->editingRoles = $user->roles->pluck('id')->toArray();
    }

    public function saveRoles($userId)
    {
        $user = User::findOrFail($userId);
        $user->roles()->sync($this->editingRoles);
        $this->editingUserId = null;
        $this->editingRoles = [];
    }

    public function toggleActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->active = !$user->active;
        $user->save();
    }

    public function render()
    {
        $users = User::with('roles')->when($this->activeOnly, fn($q) => $q->where('active', true))->get();
        $roles = Role::all();
        return view('livewire.user-management', compact('users', 'roles'));
    }
}
