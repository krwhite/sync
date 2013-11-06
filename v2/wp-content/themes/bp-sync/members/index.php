<?php locate_template( array( 'force-login.php' ), true ); ?>
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
			<?php do_action( 'bp_before_directory_members' ); ?>
			<form action="" method="post" id="members-directory-form" class="dir-form">
				<h3>
					<?php _e( 'Members Directory', 'buddypress' ); ?>
				</h3>
				<?php do_action( 'bp_before_directory_members_content' ); ?>
				<div id="members-dir-search" class="dir-search" role="search">
					<?php bp_directory_members_search_form(); ?>
				</div>
				<!-- #members-dir-search -->
				
				<?php do_action( 'bp_before_directory_members_tabs' ); ?>
				<div class="item-list-tabs" role="navigation">
					<ul>
						<li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>
						<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
						<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>
						<?php endif; ?>
						<?php do_action( 'bp_members_directory_member_types' ); ?>
					</ul>
				</div>
				<!-- .item-list-tabs -->
				
				<div class="item-list-tabs" id="subnav" role="navigation">
					<ul>
						<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
						<li id="members-order-select" class="last filter">
							<label for="members-order-by">
								<?php _e( 'Order By:', 'buddypress' ); ?>
							</label>
							<select id="members-order-by">
								<option value="active">
								<?php _e( 'Last Active', 'buddypress' ); ?>
								</option>
								<option value="newest">
								<?php _e( 'Newest Registered', 'buddypress' ); ?>
								</option>
								<?php if ( bp_is_active( 'xprofile' ) ) : ?>
								<option value="alphabetical">
								<?php _e( 'Alphabetical', 'buddypress' ); ?>
								</option>
								<?php endif; ?>
								<?php do_action( 'bp_members_directory_order_options' ); ?>
							</select>
						</li>
					</ul>
				</div>
				<div id="members-dir-list" class="members dir-list">
					<?php locate_template( array( 'members/members-loop.php' ), true ); ?>
				</div>
				<!-- #members-dir-list -->
				
				<?php do_action( 'bp_directory_members_content' ); ?>
				<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>
				<?php do_action( 'bp_after_directory_members_content' ); ?>
			</form>
			<!-- #members-directory-form -->
			
			<?php do_action( 'bp_after_directory_members' ); ?>
		</div><!-- #pageContent --> 
	<?php do_action( 'bp_after_directory_members_page' ); ?>
	</div><!-- #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
</html>
