<?php

namespace App\Enums;

class FollowEnum
{
    public const USER1_FOLLOWS_USER2 = 1 << 0; // 000001 = 1
    public const USER2_FOLLOWS_USER1 = 1 << 1; // 000010 = 2
    public const USER1_BANNED_USER2 = 1 << 2; // 0000100 = 4
    public const USER2_BANNED_USER1 = 1 << 3; // 001000 = 8
    public const USER1_NOTIFICATIONS_ON_FOR_USER2 = 1 << 4; // 010000 = 16
    public const USER2_NOTIFICATIONS_ON_FOR_USER1 = 1 << 5; // 100000 = 32
}
