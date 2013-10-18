<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="content">
		<div class="padder">
		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'Groups Directory', 'buddypress' ); ?><?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a><?php endif; ?></h3>

			<?php do_action( 'bp_before_directory_groups_content' ); ?>

			<div id="group-dir-search" class="dir-search" role="search">

				<?php bp_directory_groups_search_form(); ?>

			</div><!-- #group-dir-search -->

			<?php do_action( 'template_notices' ); ?>





			<div id="groups-dir-list" class="groups dir-list">

				<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

			</div><!-- #groups-dir-list -->

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		</form><!-- #groups-directory-form -->

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<!-- php   get_sidebar( 'buddypress' );   wuz here -->

<?php get_footer( 'buddypress' ); ?>

