<?php

namespace App\Notifications;

trait WithMessageLink
{
    public function getMessageUrl()
    {
        return config('notification.message.url');
    }
}
