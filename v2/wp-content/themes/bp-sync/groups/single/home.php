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

		<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

		<?php do_action( 'bp_before_group_home_content' ); ?>

		<div id="item-header" role="complementary">

			<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>

		</div><!-- #item-header -->
		<div class="column size1of3">
			<div id="item-header-avatar" class="cutout">
				<a class="image-wrap" href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">
			
					<?php bp_group_avatar(); ?>
			
				</a>
			</div><!-- #item-header-avatar -->
		</div>
		<div class="column size2of3 group-content">
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
	
						<?php bp_get_options_nav(); ?>
	
						<?php do_action( 'bp_group_options_nav' ); ?>
	
					</ul>
				</div>
			</div><!-- #item-nav -->
	
			<div id="item-body">
	
				<?php do_action( 'bp_before_group_body' );
	
				/**
				 * Does this next bit look familiar? If not, go check out WordPress's
				 * /wp-includes/template-loader.php file.
				 *
				 * @todo A real template hierarchy? Gasp!
				 */
	
				// Group is visible
				if ( bp_group_is_visible() ) :
	
					// Looking at home location
					if ( bp_is_group_home() ) :
	
						// Use custom front if one exists
						$custom_front = locate_template( array( 'groups/single/front.php' ) );
						if     ( ! empty( $custom_front   ) ) : load_template( $custom_front, true );
	
						// Default to activity
						elseif ( bp_is_active( 'activity' ) ) : locate_template( array( 'groups/single/activity.php' ), true );
	
						// Otherwise show members
						elseif ( bp_is_active( 'members'  ) ) : locate_template( array( 'groups/single/members.php'  ), true );
	
						endif;
	
					// Not looking at home
					else :
	
						// Group Admin
						if     ( bp_is_group_admin_page() ) : locate_template( array( 'groups/single/admin.php'        ), true );
	
						// Group Activity
						elseif ( bp_is_group_activity()   ) : locate_template( array( 'groups/single/activity.php'     ), true );
	
						// Group Members
						elseif ( bp_is_group_members()    ) : locate_template( array( 'groups/single/members.php'      ), true );
	
						// Group Invitations
						elseif ( bp_is_group_invites()    ) : locate_template( array( 'groups/single/send-invites.php' ), true );
	
						// Old group forums
						elseif ( bp_is_group_forum()      ) : locate_template( array( 'groups/single/forum.php'        ), true );
	
						// Anything else (plugins mostly)
						else                                : locate_template( array( 'groups/single/plugins.php'      ), true );
	
						endif;
					endif;
	
				// Group is not visible
				elseif ( ! bp_group_is_visible() ) :
					// Membership request
					if ( bp_is_group_membership_request() ) :
						locate_template( array( 'groups/single/request-membership.php' ), true );
	
					// The group is not visible, show the status message
					else :
	
						do_action( 'bp_before_group_status_message' ); ?>
	
						<div id="message" class="info">
							<p><?php bp_group_status_message(); ?></p>
						</div>
	
						<?php do_action( 'bp_after_group_status_message' );
	
					endif;
				endif;
	
				do_action( 'bp_after_group_body' ); ?>
	
			</div><!-- #item-body -->
		</div><!-- .group-content -->

		<?php do_action( 'bp_after_group_home_content' ); ?>

		<?php endwhile; endif; ?>

	</div><!-- #pageContent -->
</div><!-- #main -->
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
<!-- End page.php (after </html>) -->
</html>