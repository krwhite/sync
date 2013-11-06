<?php locate_template( array( 'force-login.php' ), true ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- start page.php (before <!DOCTYPE>) -->
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<?php get_template_part( 'html-head' ); ?>

<body <?php body_class(); ?> id="bp-default">
<div id="pageHeader">
	<?php get_header(); ?>
</div>
<?php do_action( 'bp_before_directory_groups_page' ); ?>
<div id="main">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php get_sidebar(); ?>
	<?php endif; ?>
	<div id="pageContent">
		<?php locate_template('groups/groups-content.php', true, true); ?>
	</div><!-- End #pageContent --> 
</div><!-- End #main -->

<?php do_action( 'bp_after_directory_groups_page' ); ?>
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
</html>