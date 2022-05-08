<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use DuRoom\Api\Serializer\BasicDiscussionSerializer;
use DuRoom\Api\Serializer\PostSerializer;
use DuRoom\Approval\Access;
use DuRoom\Approval\Event\PostWasApproved;
use DuRoom\Approval\Listener;
use DuRoom\Discussion\Discussion;
use DuRoom\Extend;
use DuRoom\Post\CommentPost;
use DuRoom\Post\Event\Saving;
use DuRoom\Post\Post;
use DuRoom\Tags\Tag;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    // Discussions should be approved by default
    (new Extend\Model(Discussion::class))
        ->default('is_approved', true),

    // Posts should be approved by default
    (new Extend\Model(Post::class))
        ->default('is_approved', true),

    (new Extend\ApiSerializer(BasicDiscussionSerializer::class))
        ->attribute('isApproved', function ($serializer, Discussion $discussion) {
            return (bool) $discussion->is_approved;
        }),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->attribute('isApproved', function ($serializer, Post $post) {
            return (bool) $post->is_approved;
        })->attribute('canApprove', function (PostSerializer $serializer, Post $post) {
            return (bool) $serializer->getActor()->can('approvePosts', $post->discussion);
        }),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Event())
        ->listen(Saving::class, [Listener\ApproveContent::class, 'approvePost'])
        ->listen(Saving::class, [Listener\UnapproveNewContent::class, 'unapproveNewPosts'])
        ->listen(PostWasApproved::class, [Listener\ApproveContent::class, 'approveDiscussion']),

    (new Extend\Policy())
        ->modelPolicy(Tag::class, Access\TagPolicy::class),

    (new Extend\ModelVisibility(Post::class))
        ->scope(Access\ScopePrivatePostVisibility::class, 'viewPrivate'),

    (new Extend\ModelVisibility(Discussion::class))
        ->scope(Access\ScopePrivateDiscussionVisibility::class, 'viewPrivate'),

    (new Extend\ModelPrivate(Discussion::class))
        ->checker([Listener\UnapproveNewContent::class, 'markUnapprovedContentAsPrivate']),

    (new Extend\ModelPrivate(CommentPost::class))
        ->checker([Listener\UnapproveNewContent::class, 'markUnapprovedContentAsPrivate']),
];
