<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>
<div class="widget_nav_menu">
	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
	<h3><?php bp_displayed_user_fullname(); ?></h3>
	<div id="item-buttons">
		<?php do_action( 'bp_member_header_actions' ); ?>
	</div><!-- #item-buttons -->

	<?php endif; ?>

</div>
<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>