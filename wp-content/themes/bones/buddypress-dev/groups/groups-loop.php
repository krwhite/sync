<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination hide">
		<div class="pag-count" id="group-dir-count-top">
			<?php bp_groups_pagination_count(); ?>
		</div>
		<div class="pagination-links" id="group-dir-pag-top"> 
			<?php bp_groups_pagination_links(); ?>
		</div>
	</div>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>


		<ul id="groups-list" class="grid_view line" role="main">
		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<li class="item">
				<a class="<?php bp_group_type(); ?>" href="<?php bp_group_permalink(); ?>">	
				<div class="item-details">
					<div class="item-avatar">
					<?php bp_group_avatar( 'type=full' ); ?>
					</div>
					<div class="item-title" title="<?php bp_group_name(); ?>"><?php bp_group_name(); ?></div>
					<div class="item-meta">
						<i class="icon-globe tips" title="this group is PUBLIC"></i> 
						<i class="icon-lock tips" title="this group is PRIVATE"></i> 
						<i class="icon-eye-close tips" title="this group is HIDDEN"></i> 
						<?php bp_group_member_count(); ?>
					</div>
					
					<?php do_action( 'bp_directory_groups_item' ); ?>
				</div>
				<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>
				<div class="action">
					<?php do_action( 'bp_directory_groups_actions' ); ?>
					<div class="meta">
					<span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
					</div>
				</div>
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
