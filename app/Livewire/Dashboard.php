<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();
        return view('livewire.dashboard', compact('roles'));
    }
}
