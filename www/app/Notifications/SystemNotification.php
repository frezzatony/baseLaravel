<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'     =>  $this->message['title'],
            'text'      =>  $this->message['text'],
            'author'    =>    $this->message['author'],
        ];
    }
}
