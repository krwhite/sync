<?php do_action( 'bp_before_group_activity_content' ); ?>

<div class="activity single-group" role="main">
	<?php locate_template( array( 'forum.php' ), true ); ?>
</div><!-- .activity.single-group -->

<?php do_action( 'bp_after_group_activity_content' ); ?>
