<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>


	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list grid_view" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li class="item <?php bp_group_type(); ?>">
			<a href="<?php bp_group_permalink(); ?>">
				<span class="item-avatar">
					<?php bp_group_avatar( 'type=full' ); ?>
				</span>
				<span class="item-title"><?php bp_group_name(); ?> <?php do_action( 'bp_directory_groups_item' ); ?></span>
					
				<span class="meta">
					<i class="icon-globe tips" title="this group is PUBLIC"></i>
					<i class="icon-lock tips" title="this group is PRIVATE"></i>
					<i class="icon-eye-close tips" title="this group is HIDDEN"></i>

					<?php bp_group_member_count(); ?>
				</span>
			</a>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
