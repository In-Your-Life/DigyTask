<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class UserPreferencesForm extends Component
{
    public $per_page = 10;
    public $theme = 'light';
    public $notifications_enabled = true;
    public $email_notifications = true;
    public $granular_notifications = [];

    public function mount()
    {
        $user = Auth::user();
        $prefs = $user->userPreference;
        if ($prefs) {
            $this->per_page = $prefs->per_page;
            $this->theme = $prefs->theme;
            $this->notifications_enabled = $prefs->notifications_enabled;
            $this->email_notifications = $prefs->email_notifications;
            $this->granular_notifications = $prefs->granular_notifications ?? [];
        }
    }

    public function save()
    {
        $user = Auth::user();
        $prefs = $user->userPreference ?: new UserPreference(['user_id' => $user->id]);
        $prefs->per_page = $this->per_page;
        $prefs->theme = $this->theme;
        $prefs->notifications_enabled = $this->notifications_enabled;
        $prefs->email_notifications = $this->email_notifications;
        $prefs->granular_notifications = $this->granular_notifications;
        $prefs->user_id = $user->id;
        $prefs->save();
        session()->flash('success', 'Preferenze salvate!');
    }

    public function render()
    {
        return view('livewire.user-preferences-form');
    }
}
