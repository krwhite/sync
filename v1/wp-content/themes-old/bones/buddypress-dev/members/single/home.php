<div id="buddypress" class="line profile wrapped-content">

	<?php do_action( 'bp_before_member_home_content' ); ?>


	<!-- #item-header -->
<?php bp_get_template_part( 'members/single/member-header' ) ?>
<div id="item-header-avatar" class=" unit size1of3 profile-header">
	<div class="inner">
	<div class="cutout">
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
</div><!-- #item-header-avatar -->
<div class="lastUnit">
			<!--
		<strong class="">@<?php bp_displayed_user_username(); ?></strong>
		<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>-->
		<div class="inner">
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul class="nav nav-pills">

				<?php my_bp_get_displayed_user_nav(); ?>

				<?php do_action( 'bp_member_options_nav' ); ?>

			</ul>
		</div>
	</div><!-- #item-nav -->

	<div id="item-body">

		<?php do_action( 'bp_before_member_body' );

		if ( bp_is_user_activity() || !bp_current_component() ) :
			bp_get_template_part( 'members/single/activity' );

		elseif ( bp_is_user_blogs() ) :
			bp_get_template_part( 'members/single/blogs'    );

		elseif ( bp_is_user_friends() ) :
			bp_get_template_part( 'members/single/friends'  );

		elseif ( bp_is_user_groups() ) :
			bp_get_template_part( 'members/single/groups'   );

		elseif ( bp_is_user_messages() ) :
			bp_get_template_part( 'members/single/messages' );

		elseif ( bp_is_user_profile() ) :
			bp_get_template_part( 'members/single/profile'  );

		elseif ( bp_is_user_forums() ) :
			bp_get_template_part( 'members/single/forums'   );

		elseif ( bp_is_user_settings() ) :
			bp_get_template_part( 'members/single/settings' );

		// If nothing sticks, load a generic template
		else :
			bp_get_template_part( 'members/single/plugins'  );

		endif;

		do_action( 'bp_after_member_body' ); ?>

	</div><!-- #item-body -->

	<?php do_action( 'bp_after_member_home_content' ); ?>
</div>
</div>

</div><!-- #buddypress -->
