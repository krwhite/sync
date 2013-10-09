<?php

/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */


if ( function_exists( 'bp_post_get_permalink' ) ) { // ugly ugly ugly hack to check for pre-1.2 versions of BP

	add_action( 'wp_footer', 'invite_anyone_add_old_css' );
	?>

	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

		<?php do_action( 'bp_before_group_send_invites_content' ) ?>

			<?php if ( $event != 'create' ) : ?>
				<form action="<?php bp_group_send_invite_form_action() ?>" method="post" id="send-invite-form">
			<?php endif; ?>

				<div class="left-menu unit size1of3">

					<p><?php _e("Search for members to invite:", 'bp-invite-anyone') ?></span></p>

					<div class="first acfb-holder">

							<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
					</div>

					<p><?php _e( 'Select members from the directory:', 'bp-invite-anyone' ) ?> </p>

					<div id="invite-anyone-member-list">
						<ul>
							<?php bp_new_group_invite_member_list() ?>
						</ul>

						<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
					</div>

				</div>

				<div class="main-column lastUnit">

					<div id="message" class="info">
						<p><?php _e('Select people to invite.', 'bp-invite-anyone'); ?></p>
					</div>

					<?php do_action( 'bp_before_group_send_invites_list' ) ?>

					<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
					<ul id="invite-anyone-invite-list" class="item-list">
					<?php if ( bp_group_has_invites() ) : ?>

						<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

							<li id="<?php bp_group_invite_item_id() ?>">
								<?php bp_group_invite_user_avatar() ?>

								<h4><?php bp_group_invite_user_link() ?></h4>
								<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

								<?php do_action( 'bp_group_send_invites_item' ) ?>

								<div class="action">
									<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'buddypress' ) ?></a>

									<?php do_action( 'bp_group_send_invites_item_action' ) ?>
								</div>
							</li>

						<?php endwhile; ?>
					<?php endif; ?>
					</ul>

					<?php do_action( 'bp_after_group_send_invites_list' ) ?>

				</div>

				<div class="clear"></div>

			<?php if ( $event != 'create' ) : ?>
				<p class="clear"><input type="submit" name="submit" id="submit" value="<?php _e( 'Send Invites', 'buddypress' ) ?>" /></p>
				<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>
			<?php endif; ?>

			<input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" />

			<?php if ( $event != 'create' ) : ?>
				</form>
			<?php endif; ?>

		<?php do_action( 'bp_before_group_send_invites_content' ); ?>
	<?php endwhile; endif;

} else { // Begin BP 1.2 code

	?>

	<?php do_action( 'bp_before_group_send_invites_content' ) ?>



	<?php if ( !bp_get_new_group_id() ) : ?>
		<form action="<?php invite_anyone_group_invite_form_action() ?>" method="post" id="send-invite-form" class="line">
	<?php endif; ?>

	<div class="left-menu unit size1of3">
				<strong><?php _e("Search for members to invite", 'bp-invite-anyone') ?> </strong><br/>

		<div class="first acfb-holder">
				<i class="icon-search"></i><input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
		</div>
		<strong><?php _e( 'Or select from the directory:', 'bp-invite-anyone' ) ?></strong><br/>

		<div id="invite-anyone-member-list">
			<ul>
				<?php bp_new_group_invite_member_list() ?>
			</ul>

			<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
		</div>
	</div>

	<div class="main-column lastUnit">

		<div id="message" class="info">
			<h4>Invite List</h4>
		</div>

		<?php do_action( 'bp_before_group_send_invites_list' ) ?>

		<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
		<ul id="invite-anyone-invite-list" class="item-list">
		<?php if ( bp_group_has_invites() ) : ?>

			<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

				<li id="<?php bp_group_invite_item_id() ?>" class="line">
					<div class="unit">
						<?php bp_group_invite_user_avatar() ?>
					</div>
					<div class="unit size1of2">
						<h4><?php bp_group_invite_user_link() ?></h4>
					</div>
					<?php do_action( 'bp_group_send_invites_item' ) ?>

					<div class="action lastUnit">
						<a class="remove btn btn-danger tips" title="Remove Invite" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><i class="icon-remove"></i></a>

						<?php do_action( 'bp_group_send_invites_item_action' ) ?>
					</div>
				</li>

			<?php endwhile; ?>

		<?php endif; ?>
		</ul>

		<?php do_action( 'bp_after_group_send_invites_list' ) ?>
	<?php if ( !bp_get_new_group_id() ) : ?>
	<div class="submit">
		<input type="submit" name="submit" class="btn btn-success btn-block" id="submit" value="<?php _e( 'Send Invites', 'buddypress' ) ?>" />
	</div>
	<?php endif; ?>
	</div>

	<div class="clear"></div>



	<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>

		<!-- Don't leave out this sweet field -->
	<?php
	if ( !bp_get_new_group_id() ) {
		?><input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" /><?php
	} else {
		?><input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" /><?php
	}
	?>

	<?php if ( !bp_get_new_group_id() ) : ?>
		</form>
	<?php endif; ?>

	<?php do_action( 'bp_after_group_send_invites_content' ) ?>



<?php
}
?>
