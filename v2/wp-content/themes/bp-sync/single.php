<!-- single.php -->
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
			<?php if( is_single() ) { ?>
				<?php if( current_user_can( 'edit_posts' ) ) { ?>
					<li class="admin-edit menu">
						<span class="generic-button"><?php edit_post_link( __( 'Edit', 'buddypress' ) ); ?></span>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>
		<?php endif; ?>
		<?php do_action( 'bp_before_blog_single_post' ); ?>
			<div class="page" id="blog-single" role="main">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="post-content">
						<h2 class="page-title"><?php the_title(); ?></h2>
						<i class="date"> <?php printf( __( '%1$s', 'buddypress' ), get_the_modified_date(), get_the_category_list( ', ' ) ); ?>  </i>
						<div class="entry">
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						</div>
					</div>
				</div>
				<?php comments_template(); ?>
				<?php endwhile; else: ?>
				<p>
					<?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ); ?>
				</p>
				<?php endif; ?>
			</div>
			<?php do_action( 'bp_after_blog_single_post' ); ?>
		</div><!-- #pageContent --> 
	</div><!-- #main -->
	<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
</html>
<!-- End single.php -->