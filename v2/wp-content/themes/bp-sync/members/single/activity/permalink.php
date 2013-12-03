<?php locate_template( array( 'force-login.php' ), true ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- start page.php (before <!DOCTYPE>) -->
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<?php get_template_part( 'html-head' ); ?>

<body <?php body_class(); ?> id="bp-default">
<div id="pageHeader">
  <?php get_header(); ?>
</div>
<div id="main">
	<div id="pageContent">

	<?php do_action( 'template_notices' ); ?>

		<div class="activity no-ajax" role="main">
			<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>
		
				<ul id="activity-stream" class="activity-list item-list">
				<?php while ( bp_activities() ) : bp_the_activity(); ?>
		
					<?php locate_template( array( 'activity/entry.php' ), true ); ?>
		
				<?php endwhile; ?>
				</ul>
		
			<?php endif; ?>
		</div>

	</div><!-- #pageContent -->
</div><!-- #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>