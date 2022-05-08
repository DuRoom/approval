import { extend, override } from 'duroom/common/extend';
import app from 'duroom/forum/app';
import Discussion from 'duroom/common/models/Discussion';
import Post from 'duroom/common/models/Post';
import Badge from 'duroom/common/components/Badge';
import DiscussionListItem from 'duroom/forum/components/DiscussionListItem';
import PostComponent from 'duroom/forum/components/Post';
import CommentPost from 'duroom/forum/components/CommentPost';
import Button from 'duroom/common/components/Button';
import PostControls from 'duroom/forum/utils/PostControls';

app.initializers.add(
  'duroom-approval',
  () => {
    Discussion.prototype.isApproved = Discussion.attribute('isApproved');

    extend(Discussion.prototype, 'badges', function (items) {
      if (!this.isApproved() && !items.has('hidden')) {
        items.add(
          'awaitingApproval',
          <Badge type="awaitingApproval" icon="fas fa-gavel" label={app.translator.trans('duroom-approval.forum.badge.awaiting_approval_tooltip')} />
        );
      }
    });

    Post.prototype.isApproved = Post.attribute('isApproved');
    Post.prototype.canApprove = Post.attribute('canApprove');

    extend(DiscussionListItem.prototype, 'elementAttrs', function (attrs) {
      if (!this.attrs.discussion.isApproved()) {
        attrs.className += ' DiscussionListItem--unapproved';
      }
    });

    extend(PostComponent.prototype, 'elementAttrs', function (attrs) {
      if (!this.attrs.post.isApproved()) {
        attrs.className += ' Post--unapproved';
      }
    });

    extend(CommentPost.prototype, 'headerItems', function (items) {
      if (!this.attrs.post.isApproved() && !this.attrs.post.isHidden()) {
        items.add('unapproved', app.translator.trans('duroom-approval.forum.post.awaiting_approval_text'));
      }
    });

    override(PostComponent.prototype, 'flagReason', function (original, flag) {
      if (flag.type() === 'approval') {
        return app.translator.trans('duroom-approval.forum.post.awaiting_approval_text');
      }

      return original(flag);
    });

    extend(PostControls, 'destructiveControls', function (items, post) {
      if (!post.isApproved() && post.canApprove()) {
        items.add(
          'approve',
          <Button icon="fas fa-check" onclick={PostControls.approveAction.bind(post)}>
            {app.translator.trans('duroom-approval.forum.post_controls.approve_button')}
          </Button>,
          10
        );
      }
    });

    PostControls.approveAction = function () {
      this.save({ isApproved: true });

      if (this.number() === 1) {
        this.discussion().pushAttributes({ isApproved: true });
      }
    };
  },
  -10
); // set initializer priority to run after reports
