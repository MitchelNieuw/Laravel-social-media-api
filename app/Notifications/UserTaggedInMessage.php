<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserTaggedInMessage extends Notification
{
    use Queueable, WithMessageLink;

    public function __construct(
        protected string $userTag,
        protected Message $message
    )
    {
    }

    public function via(): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(): array
    {
        return [
            'notification_id' => $this->id,
            'messageId' => $this->message->id,
            'message' => '@' . $this->userTag . ' tagged you in a new message',
            'link' => "{$this->getMessageUrl()}/$this->userTag/message/{$this->message->id}",
        ];
    }
}
