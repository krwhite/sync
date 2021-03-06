<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- start page.php (before <!DOCTYPE>) -->
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
	<?php if ( is_active_sidebar( 'content_widgets_top' ) ) : ?>
		<ul id="contentWidgets">
			<?php dynamic_sidebar( 'content_widgets_top' ); ?>
		</ul>
	<?php endif; ?>

	<?php global $wp_query;
		$wp_query->is_search=true;
		$search_term=$_REQUEST['search-terms'];
		if(empty($search_term))
			$search_term=$_REQUEST['s'];
		$wp_query->query("s=".$search_term);?>

    <h2 class="pagetitle">
		Search Results for <?php echo sprintf(__("'%s'","bpmag"),$search_term);?>
    </h2>
	<?php do_action("advance-search");//this is the only line you need?>
  </div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>