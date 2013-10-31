<?php do_action( 'bp_before_directory_groups' ); ?>

<form action="" method="post" id="groups-directory-form" class="dir-form">
	<div class="widget_nav_menu">
		<h3><?php _e( 'Groups', 'buddypress' ); ?></h3>
		<?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?>
			&nbsp;<a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>" title="Add a new group">
			<i class="icon-plus"></i> <?php _e( 'New Group', 'buddypress' ); ?>
			</a>
			<?php endif; ?>
	</div>
	<?php do_action( 'bp_before_directory_groups_content' ); ?>
	<!-- #group-dir-search -->
	
	<?php do_action( 'template_notices' ); ?>
	<!-- .item-list-tabs -->
	
	<div id="groups-dir-list" class="groups dir-list">
		<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>
	</div>
	<!-- #groups-dir-list -->
	
	<?php do_action( 'bp_directory_groups_content' ); ?>
	<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>
	<?php do_action( 'bp_after_directory_groups_content' ); ?>
</form>
<!-- #groups-directory-form -->

<?php do_action( 'bp_after_directory_groups' ); ?>
