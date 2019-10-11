<?php

namespace App\Enums;

/**
 * @package App\Enums
 */
class FollowEnum
{
    public const USER1_FOLLOWS_USER2 = 1 << 0; // 000001 = 1
    public const USER2_FOLLOWS_USER1 = 1 << 1; // 000010 = 2
    public const USER1_BANNED_USER2 = 1 << 2; // 0000100 = 4
    public const USER2_BANNED_USER1 = 1 << 3; // 001000 = 8
    public const USER1_NOTIFICATIONS_ON_FOR_USER2 = 1 << 4; // 010000 = 16
    public const USER2_NOTIFICATIONS_ON_FOR_USER1 = 1 << 5; // 100000 = 32




    // SELECT * FROM follow WHERE user_id = $userId AND followers status&2=2 AND NOT status&8=8
    // returns => 2, 3, 6, 7
    // SELECT * FROM follow WHERE follow_user_id = $userId AND followers status&1=1 AND NOT status&4=4
    // returns => 1, 3, 9, 11

    // Check if following is possible
    // SELECT * FROM `follow`
    //   WHERE user_id=1 AND follow_user_id=2
    //   AND NOT (status& 4=4 OR status& 1=1 OR status& 3=3)
    //   OR follow_user_id=1 AND user_id=2
    //   AND NOT (status& 8=8 OR status& 2=2);
    //
    //  returns => 2, 8, 10, 16

    /*
     * tableName:
     * ID    user1        user2      |   follows
     * 1     1            2          |    1            => 000001
     * 2     1            2          |    2            => 000010
     * 3     1            2          |    3            => 000011
     * 4     1            2          |    4            => 000100
     * 5     1            2          |    5            => 000101
     * 6     1            2          |    6            => 000110
     * 7     1            2          |    7            => 000111
     * 8     1            2          |    8            => 001000
     * 9     1            2          |    9            => 001001
     * 10    1            2          |   10            => 001010
     * 11    1            2          |   11            => 001011
     * 12    1            2          |   12            => 001100
     * 13    1            2          |   13            => 001101
     * 14    1            2          |   14            => 001110
     * 15    1            2          |   15            => 001111
     * 16    1            2          |   16            => 010000
     *
     * protected $q = 'SELECT * FROM `tableName` WHERE user1=1 AND follows&1=1'; // gevonden: 1, 3, 5, 7, 9, 11 <= te veel
     * protected $q2 = 'SELECT * FROM `tableName` WHERE user2=2 AND follows&2=2'; // gevonden: 2, 3, 6, 7, 10, 11 <= te veel
     * protected $q3 = 'SELECT * FROM `tableName` WHERE user1=1 AND follows&1=1 AND NOT follows&8=8';//gevonden: 1, 3, 5, 7
     * protected $q3 = 'SELECT * FROM `tableName` WHERE user2=2 AND follows&2=2 AND NOT follows&4=4';//gevonden: 2, 3, 10, 11
     *
     * INSERT INTO `follow` (`id`, `user_id`, `follow_user_id`, `status`, `created_at`, `updated_at`) VALUES
     *   (22,1,2,4,NULL,NULL),
     *   (23,1,2,5,NULL,NULL),
     *   (24,1,2,6,NULL,NULL),
     *   (25,1,2,7,NULL,NULL),
     *   (26,1,2,8,'2019-10-04 12:07:17','2019-10-04 12:07:21'),
     *   (27,1,2,9,NULL,NULL),
     *   (28,1,2,10,'2019-10-04 12:07:26','2019-10-04 12:07:29'),
     *   (29,1,2,11,NULL,NULL),
     *   (30,1,2,12,NULL,NULL),
     *   (31,1,2,13,NULL,NULL),
     *   (32,1,2,14,NULL,NULL),
     *   (33,1,2,15,NULL,NULL),
     *   (34,1,2,16,'2019-10-04 12:07:32','2019-10-04 12:07:34');
    */
}