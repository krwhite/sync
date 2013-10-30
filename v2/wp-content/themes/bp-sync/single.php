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
			<?php do_action( 'bp_before_blog_single_post' ); ?>
			<div class="page" id="blog-single" role="main">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="author-box"> <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
						<p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), str_replace( '<a href=', '<a rel="author" href=', bp_core_get_userlink( $post->post_author ) ) ); ?></p>
					</div>
					<div class="post-content">
						<h2 class="posttitle">
							<?php the_title(); ?>
						</h2>
						<p class="date"> <?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?> <span class="post-utility alignright">
							<?php edit_post_link( __( 'Edit this entry', 'buddypress' ) ); ?>
							</span> </p>
						<div class="entry">
							<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						</div>
						<p class="postmetadata">
							<?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>
							&nbsp;</p>
						<div class="alignleft">
							<?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'buddypress' ) . '</span> %title' ); ?>
						</div>
						<div class="alignright">
							<?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'buddypress' ) . '</span>' ); ?>
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