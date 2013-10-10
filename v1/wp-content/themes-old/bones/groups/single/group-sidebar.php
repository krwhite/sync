<?php

do_action( 'bp_before_group_header' );

?>

<div id="item-header-avatar" class="cutout">
	<a class="image-wrap" href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">

		<?php bp_group_avatar(); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">
	<h3 class="<?php bp_group_type(); ?> icon">
		<a href="http://codex.buddypress.org/user/setting-up-a-new-installation/group-settings-and-roles/">
			<i class="icon-globe tips" title="this group is PUBLIC"></i> 
			<i class="icon-lock tips" title="this group is PRIVATE"></i> 
			<i class="icon-eye-close tips" title="this group is HIDDEN"></i> 
		</a>
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

		<h5><?php _e( 'Group Admins', 'buddypress' ); ?></h5>

		<?php bp_group_list_admins();

		do_action( 'bp_after_group_menu_admins' );

		if ( bp_group_has_moderators() ) :
			do_action( 'bp_before_group_menu_mods' ); ?>

			<h3><?php _e( 'Group Mods' , 'buddypress' ); ?></h3>

			<?php bp_group_list_mods();

			do_action( 'bp_after_group_menu_mods' );

		endif;

	endif; ?>

</div><!-- #item-actions -->

<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
?>