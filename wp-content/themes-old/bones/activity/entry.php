<?php

/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_activity_entry' ); ?>

<div class="<?php bp_activity_css_class(); ?> line is-searchable" id="activity-<?php bp_activity_id(); ?>">
	<div class="activity-avatar unit">
		<a href="<?php bp_activity_user_link(); ?>">
			<?php bp_activity_avatar("type=full"); ?>
		</a>
	</div>

	<div class="activity-content lastUnit hover">

		<div class="activity-header line">

			<?php bp_activity_action(); ?>

		</div>

		<?php if ( 'activity_comment' == bp_get_activity_type() ) : ?>

			<div class="activity-inreplyto">
				<strong><?php _e( 'In reply to: ', 'buddypress' ); ?></strong><?php bp_activity_parent_content(); ?> 
					<a href="<?php bp_activity_thread_permalink(); ?>" class="view" title="<?php _e( 'View Thread / Permalink', 'buddypress' ); ?>"><?php _e( 'View', 'buddypress' ); ?></a>
			</div>

		<?php endif; ?>

		<?php if ( bp_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php bp_activity_content_body(); ?>

			</div>

		<?php endif; ?>

		<?php do_action( 'bp_activity_entry_content' ); ?>

		<?php if ( is_user_logged_in() ) : ?>

			<div class="activity-meta line">
				<?php if ( bp_activity_can_favorite() ) : ?>

					<?php if ( !bp_get_activity_is_favorite() ) : ?>

						<a href="<?php bp_activity_favorite_link(); ?>" class=" fav bp-secondary-action" title="<?php esc_attr_e( 'Mark as Favorite', 'buddypress' ); ?>"><i class="icon-star-empty"></i> <span>Favorite</span></a>

					<?php else : ?>

						<a href="<?php bp_activity_unfavorite_link(); ?>" class=" unfav bp-secondary-action" title="<?php esc_attr_e( 'Remove Favorite', 'buddypress' ); ?>"><i class="icon-star"></i> <span>Unfavorite</span></a>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( bp_activity_can_comment() ) : ?>

					<a href="<?php bp_activity_comment_link(); ?>" class=" acomment-reply bp-primary-action" id="acomment-comment-<?php bp_activity_id(); ?>"><i class="icon-reply"></i> <span>Reply</span>

				<?php endif; ?>


				<?php if ( bp_activity_user_can_delete() ) bp_delete_link_custom(); ?>

				<?php do_action( 'bp_activity_entry_meta' ); ?>

			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'bp_before_activity_entry_comments' ); ?>

	<?php if ( ( is_user_logged_in() && bp_activity_can_comment() ) || bp_activity_get_comment_count() ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() ) : ?>

				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?> class="hidden">
					<div class="ac-reply-avatar unit"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content lastUnit">
						<div class="ac-textarea">
							<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
							<input class="btn btn-success" type="submit" name="ac_form_submit" value="<?php _e( 'Post', 'buddypress' ); ?>" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ); ?>
						
						</div>
						<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
					</div>

					<?php do_action( 'bp_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php do_action( 'bp_after_activity_entry_comments' ); ?>

</div>

<?php do_action( 'bp_after_activity_entry' ); ?>
