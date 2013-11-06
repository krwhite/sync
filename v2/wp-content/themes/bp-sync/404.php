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
			<div class="widget_nav_menu">
				<h3><?php _e("We&rsquo;re onto you, buddy.", "buddypress"); ?></h3>
			</div>						
					
			<section class="entry-content">
				<p><img src="/wp-content/themes/bp-sync/bp_default_avatar.png" /></p>
				<p><?php _e("You&rsquo;ve stumbled upon an area we didn&rsquo;t think you&rsquo;d find.", "buddypress"); ?></p>
				<p><?php _e("Perhaps you could use some", "buddypress"); ?> <a href="/help"><?php _e("help?", "buddypress"); ?></a></p>
				<p><?php _e("Or try using that search up there. Trust me, it's good.", "buddypress"); ?></p>
	
			</section> <!-- end article section -->



	</div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>