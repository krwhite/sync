<!-- footer.php -->
  <?php do_action( 'bp_after_container' ); ?>
  <?php do_action( 'bp_before_footer'   ); ?>
  <?php if ( is_active_sidebar( 'first-footer-widget-area' ) || is_active_sidebar( 'second-footer-widget-area' ) || is_active_sidebar( 'third-footer-widget-area' ) || is_active_sidebar( 'fourth-footer-widget-area' ) ) : ?>
  <div id="footer-widgets">
    <?php get_sidebar( 'footer' ); ?>
  </div>
  <?php endif; ?>
  <?php do_action( 'bp_footer' ); ?>
  <?php do_action( 'bp_after_footer' ); ?>
  <?php wp_footer(); ?>
<!-- End footer.php -->