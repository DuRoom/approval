<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Approval\Event;

use DuRoom\Post\Post;
use DuRoom\User\User;

class PostWasApproved
{
    /**
     * The post that was approved.
     *
     * @var Post
     */
    public $post;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param Post $post
     * @param User $actor
     */
    public function __construct(Post $post, User $actor)
    {
        $this->post = $post;
        $this->actor = $actor;
    }
}
