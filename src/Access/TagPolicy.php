<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Approval\Access;

use DuRoom\Tags\Tag;
use DuRoom\User\Access\AbstractPolicy;
use DuRoom\User\User;

class TagPolicy extends AbstractPolicy
{
    /**
     * @return bool|null
     */
    public function addToDiscussion(User $actor, Tag $tag)
    {
        return $actor->can('discussion.startWithoutApproval', $tag);
    }
}
