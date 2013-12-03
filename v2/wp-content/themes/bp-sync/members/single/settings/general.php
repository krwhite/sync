<?php locate_template( array( 'force-login.php' ), true ); ?>
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

			<?php do_action( 'bp_before_member_settings_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->
			<div class="column size1of3">
				<div class="inner">
					<div id="item-header-avatar" class="cutout">
						<?php bp_displayed_user_avatar( 'type=full&width=100%&height=' ); ?>
					</div><!-- #item-header-avatar -->
					
					<p>Use <span class="user-nicename">@<?php bp_displayed_user_username(); ?></span> in comments to reply with a  notification</p>
					<div id="item-header-content">
						<?php do_action( 'bp_before_member_header_meta' ); ?>
						<div id="item-meta">

							<?php if ( bp_is_active( 'activity' ) ) : ?>
					
								<div id="latest-update">
					
									<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
					
								</div>
					
							<?php endif; ?>
					
					
							<?php
							/***
							 * If you'd like to show specific profile fields here use:
							 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
							 */
							 do_action( 'bp_profile_header_meta' );
					
							 ?>
					
						</div><!-- #item-meta -->
					</div><!-- #item-header-content -->
				</div>
			</div>
			<div class="column size2of3">
				<div id="item-nav">
					<div class="item-list-tabs no-ajax nav-tabs" id="object-nav" role="navigation">
						<ul>
	
							<?php bp_get_displayed_user_nav(); ?>
	
							<?php do_action( 'bp_member_options_nav' ); ?>
	
						</ul>
					</div>
				</div><!-- #item-nav -->
	
				<div id="item-body">

					<?php do_action( 'bp_before_member_settings_template' ); ?>
		
						<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

						<?php do_action( 'bp_before_member_body' ); ?>
		
						<div class="item-list-tabs no-ajax" id="subnav">
							<ul>
		
								<?php bp_get_options_nav(); ?>
		
								<?php do_action( 'bp_member_plugin_options_nav' ); ?>
		
							</ul>
						</div><!-- .item-list-tabs -->
		
						<h3><?php _e( 'General Settings', 'buddypress' ); ?></h3>
		
						<?php do_action( 'bp_template_content' ); ?>
		
							<p><label for="email"><?php _e( 'Account Email', 'buddypress' ); ?>:</label> 
								<strong name="email" id="email"><?php echo bp_get_displayed_user_email(); ?></strong></p>	
							<p>Sync uses your Asynchrony Active Directory account. Visit the Security Management site if you need to <a href="https://pwchng.asynchrony.com/" target="_blank">change your password</a>.</p>

						<?php do_action( 'bp_after_member_body' ); ?>

				</div><!-- #item-body -->
			</div><!-- .column -->
			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div><!-- #pageContent -->
	</div><!-- #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>
