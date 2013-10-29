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
	  <?php get_sidebar(); ?>
  <?php endif; ?>
  <div id="pageContent">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h2 class="pagetitle">
      <?php the_title(); ?>
    </h2>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <div class="entry">
        <?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
        <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
        <?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="edit-link">', '</p>'); ?>
      </div>
    </div>
    <?php comments_template(); ?>
    <?php endwhile; endif; ?>
  </div><!-- End #pageContent -->
</div><!-- End #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>