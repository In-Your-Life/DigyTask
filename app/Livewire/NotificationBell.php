<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationBell extends Component
{
    public $showDropdown = false;

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function render()
    {
        $userId = Auth::id();
        $notifications = DatabaseNotification::where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->latest()
            ->limit(10)
            ->get();
        $unreadCount = $notifications->count();
        return view('livewire.notification-bell', compact('notifications', 'unreadCount'));
    }
}
