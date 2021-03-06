<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use DuRoom\Database\Migration;
use DuRoom\Group\Group;

return Migration::addPermissions([
    'discussion.startWithoutApproval' => Group::MEMBER_ID,
    'discussion.replyWithoutApproval' => Group::MEMBER_ID,
    'discussion.approvePosts' => Group::MODERATOR_ID
]);
