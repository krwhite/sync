<!-- header.php -->
<?php do_action( 'bp_before_header' ); ?>

<div id="search-bar" role="search">
  <div class="padder">
    <h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>">
      <?php bp_site_name(); ?>
      </a></h1>

    <!-- #search-form -->
    
    <?php do_action( 'bp_search_login_bar' ); ?>
    <?php dynamic_sidebar( 'header-widgets' ); ?>
  </div>
  <!-- .padder --> 
</div>
<!-- #search-bar -->
<div class="avatar-container pull-right">
  <div id="sidebar-me"> <a href="<?php echo bp_loggedin_user_domain(); ?>">
    <?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ); ?>
    </a>
    <h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
    <a class="button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>">
    <?php _e( 'Log Out', 'buddypress' ); ?>
    </a>
    <?php do_action( 'bp_sidebar_me' ); ?>
  </div>
</div>
<!-- .avatar-container -->

<div id="navigation" role="navigation">
  <?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); ?>
</div>
<?php do_action( 'bp_header' ); ?>
<?php do_action( 'bp_after_header'     ); ?>
<?php do_action( 'bp_before_container' ); ?>
<!-- End header.php -->