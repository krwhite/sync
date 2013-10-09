<?php
 
/**
 * BuddyPress - Users Sidebar
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>



<div class="inner">
	<div class=" cutout">
		<a href="<?php bp_displayed_user_link(); ?>" class="image-wrap"><?php bp_displayed_user_avatar( 'type=full' ); ?></a>
	</div>
	<div class="">		
		<?php do_action( 'bp_after_member_header' ); ?>
		<?php do_action( 'template_notices' ); ?>
	<div id="item-header-content" class="hidden">
				<?php do_action( 'bp_before_member_header_meta' ); ?>
				<div id="item-meta">
					<?php if ( bp_is_active( 'activity' ) ) : ?>
						<div id="latest-update">
							<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
						</div>
					<?php endif; ?>
					<div id="item-buttons"><?php do_action( 'bp_member_header_actions' ); ?></div><!-- #item-buttons -->
					<?php
					/***
					 * If you'd like to show specific profile fields here use:
					 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
					 */
					 do_action( 'bp_profile_header_meta' );?>
				</div><!-- #item-meta -->
			</div><!-- #item-header-content -->


	</div>
	<div class="skills">
	
				<h4>Skills</h4>
				<?php 
					$skills = get_the_author_meta('skills', 1); 
					echo '<div class="tag-list" data-tags="'.$skills.'"></div>';

					?> 
	</div>
</div>