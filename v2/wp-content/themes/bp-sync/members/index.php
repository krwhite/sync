<?php locate_template( array( 'force-login.php' ), true ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<?php get_template_part( 'html-head' ); ?>

<body <?php body_class(); ?> id="bp-default">
<div id="pageHeader">
  <?php get_header(); ?>
</div>

	<div id="main">
		<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	  	<?php get_sidebar('sidebar-1'); ?>
		<?php endif; ?>
		<div id="pageContent">
			<?php do_action( 'bp_before_directory_members' ); ?>
			<form action="" method="post" id="members-directory-form" class="dir-form">
				<div class="widget_nav_menu"><h3><?php _e( 'People', 'buddypress' ); ?> <i><?php echo bp_get_total_member_count();?></i></h3></div>
				<?php do_action( 'bp_before_directory_members_content' ); ?>
				<!-- #members-dir-search -->
				<!-- .item-list-tabs -->
				
				<div id="members-dir-list" class="members dir-list">
					<?php locate_template( array( 'members/members-loop.php' ), true ); ?>
				</div>
				<!-- #members-dir-list -->
				
				<?php do_action( 'bp_directory_members_content' ); ?>
				<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>
				<?php do_action( 'bp_after_directory_members_content' ); ?>
			</form>
			<!-- #members-directory-form -->
			
			<?php do_action( 'bp_after_directory_members' ); ?>
		</div><!-- #pageContent --> 
	<?php do_action( 'bp_after_directory_members_page' ); ?>
	</div><!-- #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
</html>
