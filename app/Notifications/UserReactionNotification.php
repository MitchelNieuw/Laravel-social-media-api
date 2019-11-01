<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * @package App\Notifications
 */
class UserReactionNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    protected $userTag;

    /**
     * @var int
     */
    protected $messageId;

    /**
     * @param string $userTag
     * @param int $messageId
     */
    public function __construct(string $userTag, int $messageId)
    {
        $this->userTag = $userTag;
        $this->messageId = $messageId;
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'notification_id' => $this->id,
            'messageId' => $this->messageId,
            'message' => '@' . $this->userTag . ' gave a reaction on your message',
            'link' => env('NOTIFICATION_MESSAGE_URL') . $this->userTag . '/message/' . $this->messageId,
        ];
    }
}
