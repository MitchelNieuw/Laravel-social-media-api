<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * @package App\Notifications
 */
class UserNewMessageNotification extends Notification
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
     * @return array
     */
    public function via(): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'notification_id' => $this->id,
            'messageId' => $this->messageId,
            'message' => '@'.$this->userTag.' placed a new message',
            'link' => url('/user') . '/' . $this->userTag . '/message/' . $this->messageId,
        ];
    }
}
