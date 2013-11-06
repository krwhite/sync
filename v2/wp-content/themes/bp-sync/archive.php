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
			<?php if ( is_active_sidebar( 'content_widgets_top' ) ) : ?>
				<ul id="contentWidgets">
					<?php dynamic_sidebar( 'content_widgets_top' ); ?>
				</ul>
			<?php endif; ?>

		<?php do_action( 'bp_before_archive' ); ?>

		<div class="page" id="blog-archives" role="main">

			<h1><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></h1>

			<?php if ( have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


						<div class="post-content">
							<strong class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></strong>

							<div class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></div>

							<div class="tag-list">
								<i class="icon-tags"></i> <?php the_tags( '<span class="tags">' . __( '', 'buddypress' ), ', ', '</span>' ); ?> 
							</div>
						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ); ?>

				<?php endwhile; ?>

				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>
				<div class="item-body">
					<p><?php _e( 'Hmmm, looks like we don&rsquo;t have anything for this category yet.', 'buddypress' ); ?></p>
				</div>
			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
  </div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>
