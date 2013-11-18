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
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ); ?>

		<div id="item-header" role="complementary">

			<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>

		</div><!-- #item-header -->
		<div class="column size1of3">
			<div class="inner">
				<div id="item-header-avatar" class="cutout">
					<a class="image-wrap" href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">
						<?php bp_group_avatar( 'type=full&width=100%&height=' ); ?>
					</a>
				</div><!-- #item-header-avatar -->
				<div id="item-header-content">
					<h3 class="<?php bp_group_type(); ?>">
						<i class="icon-globe publicity" title="this group is PUBLIC"></i> 
						<i class="icon-lock publicity" title="this group is PRIVATE"></i> 
						<i class="icon-eye-close publicity" title="this group is HIDDEN"></i> 
						<?php bp_group_name(); ?></h3>
					<?php do_action( 'bp_before_group_header_meta' ); ?>
				
					<div id="item-meta">
				
						<?php bp_group_description(); ?>
				
						<div id="item-buttons">
				
							<?php do_action( 'bp_group_header_actions' ); ?>
				
						</div><!-- #item-buttons -->
				
						<?php do_action( 'bp_group_header_meta' ); ?>
				
					</div>
					<span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
				</div><!-- #item-header-content -->
				<hr>
				<div id="item-actions">
				
					<?php if ( bp_group_is_visible() ) : ?>
				
						<h4><?php _e( 'Group Admins', 'buddypress' ); ?></h4>
				
						<?php bp_group_list_admins();
				
						do_action( 'bp_after_group_menu_admins' );
				
						if ( bp_group_has_moderators() ) :
							do_action( 'bp_before_group_menu_mods' ); ?>
				
							<h4><?php _e( 'Group Mods' , 'buddypress' ); ?></h4>
				
							<?php bp_group_list_mods();
				
							do_action( 'bp_after_group_menu_mods' );
				
						endif;
				
					endif; ?>
				
				</div><!-- #item-actions -->
			</div><!-- .inner -->
		</div><!-- .column -->


		<div class="column size2of3 group-content">
			<div id="item-nav">
				<div class="item-list-tabs no-ajax nav-tabs" id="object-nav" role="navigation">
					<ul>
						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_group_plugin_options_nav' ); ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ); ?>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_group_body' ); ?>
			</div><!-- #item-body -->

			<?php do_action( 'bp_after_group_plugin_template' ); ?>

			<?php endwhile; endif; ?>
		</div><!-- .group-content.column -->
	</div><!-- End #pageContent --> 
</div><!-- End #main -->

<?php do_action( 'bp_after_directory_groups_page' ); ?>
<div id="footer">
  <?php get_footer(); ?>
</div>
</body>
</html>