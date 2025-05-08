<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskSharedExternally extends Notification
{
    use Queueable;

    protected $task;
    protected $user;
    protected $link;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $user, $link)
    {
        $this->task = $task;
        $this->user = $user;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Task condiviso esternamente',
            'body' => $this->user->name . ' ha condiviso il task: ' . $this->task->title . ' tramite link esterno.',
            'url' => $this->link,
            'icon' => 'fa-share-alt',
        ];
    }
}
