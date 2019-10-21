<?php

namespace App\Enums;

/**
 * @package App\Enums
 */
class ResponseMessageEnum
{
    public const OOPS_SOMETHING_WENT_WRONG = 'Ooops something went wrong!';
    public const NO_MESSAGE_FOUND = 'No message found did not preform delete';
    public const USER_ALREADY_UNBANNED = 'User already unbanned';
    public const USER_NOT_BANNED = 'You have not banned this user';
    public const BANNING_SELF_NOT_POSSIBLE = 'Banning yourself is not possible';
    public const UNBANNING_SELF_NOT_POSSIBLE = 'Unbanning yourself is not possible';
    public const FOLLOWING_NOT_POSSIBLE = 'Following not possible';
    public const NOT_FOLLOWING_THIS_USER = 'You\'re  not following this user';
    public const FOLLOWING_SELF_NOT_POSSIBLE = 'Following yourself is not possible';
    public const UNFOLLOWING_SELF_NOT_POSSIBLE = 'Unfollowing yourself is not possible';


    public const BAN_SUCCESSFUL = 'Ban successful';
    public const UNBAN_SUCCESSFUL = 'Unban successful';
    public const FOLLOW_SUCCESSFUL = 'Follow successful';
    public const UNFOLLOW_SUCCESSFUL = 'Unfollow successful';
}