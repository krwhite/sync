<?php

/**
 * BuddyPress Notification Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>

	<div id="buddypress" class="wrapped-content">

		<div class="wrap line">
			<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
			<div class="unit size1of4">
				<?php locate_template( array( 'members/single/member-sidebar.php' ), true ); ?>
			</div>
			<div class="lastUnit content">
			<div class="inner">
				<?php do_action( 'bp_before_member_home_content' ); ?>
				<div id="item-nav">
					<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
						<ul class="nav nav-pills">

							<?php my_bp_get_displayed_user_nav(); ?>

							<?php do_action( 'bp_member_options_nav' ); ?>

						</ul>
					</div>
				</div><!-- #item-nav -->
				<div id="item-body" role="main">

					<?php do_action( 'bp_before_member_body' ); ?>

					<div class="item-list-tabs no-ajax" id="subnav">
						<ul class=" nav nav-pills">
					<li id="notifications-personal-li" class="current selected"><a id="notifications" href="JavaScript:void(0);">Notifications</a></li>

						</ul>
					</div><!-- .item-list-tabs -->

					<h3><?php _e( 'Email Notification', 'buddypress' ); ?></h3>

					<?php do_action( 'bp_template_content' ); ?>

					<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
						<p><?php _e( 'Send a notification by email when:', 'buddypress' ); ?></p>

						<?php do_action( 'bp_notification_settings' ); ?>

						<?php do_action( 'bp_members_notification_settings_before_submit' ); ?>

						<div class="submit">
							<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="btn btn-success" />
						</div>

						<?php do_action( 'bp_members_notification_settings_after_submit' ); ?>

						<?php wp_nonce_field('bp_settings_notifications'); ?>

					</form>

					<?php do_action( 'bp_after_member_body' ); ?>

				</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div><!-- .inner -->
		</div><!-- .padder -->
		</div><!-- .padder -->
	</div><!-- #content -->



<?php get_footer( 'buddypress' ); ?>