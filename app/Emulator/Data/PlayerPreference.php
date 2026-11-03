<?php

namespace App\Emulator\Data;

/**
 * In-game privacy and comfort preferences a player can change from the CMS.
 *
 * Every case is phrased positively ("allow ..." / "use ..."), so true always
 * means the switch is on; drivers translate to their own block or allow
 * columns and only report the preferences their schema stores.
 */
enum PlayerPreference: string
{
    case FriendRequests = 'friend_requests';
    case RoomInvites = 'room_invites';
    case Following = 'following';
    case CameraFollow = 'camera_follow';
    case Gifts = 'gifts';
    case Mimic = 'mimic';
    case OldChat = 'old_chat';
}
