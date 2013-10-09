<?php



/**

 * BuddyPress - Members Loop

 *

 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()

 *

 * @package BuddyPress

 * @subpackage bp-default

 */



?>

<?php /* Sets sort order to alphabetical, breaks sort option */
function my_query_filter_new ( $query_string ) {
$query_string .= '&per_page=20&type=alphabetical';
return $query_string;
}
add_filter( 'bp_ajax_querystring', 'my_query_filter_new' );
?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination hidden">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>



	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<div id="members-list" class="people-list" role="main">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<div class="person is-searchable <?php bp_member_name(); ?>">

			<div class="item-avatar">

				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full'); ?></a>

			</div>

			<div class="item">

				<div class="item-title" title="<?php bp_member_name(); ?>">

					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

				</div>

				<?php do_action( 'bp_directory_members_item' ); ?>

				<?php

				 /***

				  * If you want to show specific profile fields here you can,

				  * but it'll add an extra query for each member in the loop

				  * (only one regardless of the number of fields you show):

				  *

				  * bp_member_profile_data( 'field=the field name' );

				  */

				?>

			</div>

			<div class="action">

				<?php do_action( 'bp_directory_members_actions' ); ?>

			</div>

			<div class="clear"></div>

		</div>

	<?php endwhile; ?>

	</div>



	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count hidden" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">

		<p><?php _e( "No members found.", 'buddypress' ); ?></p>

	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>

