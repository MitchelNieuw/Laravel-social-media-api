<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserNewMessageNotification extends Notification
{
    use Queueable, WithMessageLink;

    public function __construct(
        protected string $userTag,
        protected int $messageId
    )
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'notification_id' => $this->id,
            'messageId' => $this->messageId,
            'message' => '@' . $this->userTag . ' placed a new message',
            'link' => "{$this->getMessageUrl()}/$this->userTag/message/$this->messageId",
        ];
    }
}
