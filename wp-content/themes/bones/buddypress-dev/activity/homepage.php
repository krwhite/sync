<?php

/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">

	<div class="activity-content">

		<div class="activity-header">

			<?php bp_activity_action(); ?>

		</div>

		<?php if ( bp_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php bp_activity_content_body(); ?>

			</div>

		<?php endif; ?>



	</div>

</li>

<?php do_action( 'bp_after_activity_entry' ); ?>
