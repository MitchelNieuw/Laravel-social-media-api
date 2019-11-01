<?php

namespace App\Notifications;

use App\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * @package App\Notifications
 */
class UserTaggedInMessage extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    protected $userTag;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @param string $userTag
     * @param Message $message
     */
    public function __construct(string $userTag, Message $message)
    {
        $this->userTag = $userTag;
        $this->message = $message;
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
            'messageId' => $this->message->getAttribute('id'),
            'message' => '@' . $this->userTag . ' tagged you in a new message',
            'link' => env('NOTIFICATION_MESSAGE_URL') .
                $this->userTag .
                '/message/' .
                $this->message->getAttribute('id'),
        ];
    }
}
