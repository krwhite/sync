<?php locate_template( array( 'force-login.php' ), true ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!-- start index.php (before <!DOCTYPE>) -->
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php endif; ?>
<title>
<?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?>
</title>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php bp_head(); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> id="bp-default">
<h1>This doesn't show up anywhere?!! (index.php)</h1>
<div id="pageHeader">
  <?php get_header(); ?>
</div>
<div id="main">
  <?php get_sidebar(); ?>
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
  </div>
</div>
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End index.php (after </html>) -->
</html>