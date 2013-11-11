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
		<em><i class="icon-filter"></i> Articles only</em>
    </h2>
	
		<?php do_action( 'bp_before_blog_search' ); ?>

		<div class="search-result" id="blog-search" role="main">

			<?php if (have_posts()) : ?>


				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


						<div class="post-content"> 
							<h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
							<div> 
								<?php the_excerpt();?>                           
							</div>                       
							<div class="clear"> </div>
						</div>
						<div class="postmetadata"> 
							<span class="date"><?php the_time('F j, Y') ?></span> <span class="category"><?php the_category(', ') ?></span>
						</div>
	
					</div>

					<?php do_action( 'bp_after_blog_post' ); ?>

				<?php endwhile; ?>


			<?php else : ?>

				<div class="post">
					<div class="post-content 404">
					<?php echo sprintf(__("Man, we looked everywhere but nothing matches '%s'","bpmag"),$search_term);?>

					<?php locate_template( array( 'searchform.php' ), true ) ?>
					</div>
				</div>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_search' ); ?>


  </div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>