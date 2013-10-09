<?php



/**

 * BuddyPress - Groups Directory

 *

 * @package BuddyPress

 * @subpackage bp-default

 */



get_header( 'buddypress' ); ?> 



	<?php do_action( 'bp_before_directory_groups_page' ); ?>



	<div id="buddypress" class="wrapped-content">

		<div id="inner-content" class="wrap line">



		<?php do_action( 'bp_before_directory_groups' ); ?>



		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<div class="line page_header">

				<div class="unit">

					<h1><?php the_title(); ?></h1>

				</div>

				<!-- <div class="item-list-tabs unit" role="navigation">

						<ul class="nav nav-pills">

							<li class="selected" id="groups-all"><a href="<?php bp_groups_directory_permalink(); ?>"><?php printf( __( 'All', 'buddypress' ), bp_get_total_group_count() ); ?></a></li>

							<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

								<li id="groups-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups/'; ?>"><?php printf( __( 'My Groups', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

							<?php do_action( 'bp_groups_directory_group_filter' ); ?>

						</ul>

				</div> -->

				<div class="pull-right section-actions">

					<?php 

						if ( is_user_logged_in() && bp_user_can_create_groups() ) {

					echo '<a class="btn btn-success action pull-right btn-mini tips" title="create new group" href="' . trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ) . 'create"><i class="icon-plus"></i> ' . __( 'new group', 'buddypress' ) . '</a>';

						}

					?>

					<!--

					<a href="#" id="viewList" class="pull-right action tips" title="view as list"><i class="icon-reorder"></i></a>

					<a href="#" id="viewGrid" class="pull-right action hide tips" title="view as grid"><i class="icon-th"></i></a>

					<a href="#" id="searchGroups" class="pull-right action tips" title="search groups"><i class="icon-search"></i>

						<div id="group-dir-search" class="dir-search action-menu hide" role="search">

							<?php bp_directory_groups_search_form(); ?>

						</div>

					</a>

					<a href="#" id="filterGroups" class="pull-right action tips" title="filter groups"><i class="icon-filter"></i>

						<div class="action-menu">

							<?php do_action( 'bp_groups_directory_group_types' ); ?>

							<select id="groups-order-by">

										<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>

										<option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>

										<option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>

										<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

										<?php do_action( 'bp_groups_directory_order_options' ); ?>

							</select>

						</div>

					</a>

				-->

				</div>

			</div>





			<?php do_action( 'bp_before_directory_groups_content' ); ?>



			<div id="group-dir-search" class="dir-search hidden" role="search">



				<?php bp_directory_groups_search_form(); ?>



			</div><!-- #group-dir-search -->



			<?php do_action( 'template_notices' ); ?>



			<div class="item-list-tabs hidden" role="navigation">

				<ul>

					<li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count() ); ?></a></li>



					<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>



						<li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>



					<?php endif; ?>



					<?php do_action( 'bp_groups_directory_group_filter' ); ?>



				</ul>

			</div><!-- .item-list-tabs -->



			<div class="item-list-tabs hidden" id="subnav" role="navigation">

				<ul>



					<?php do_action( 'bp_groups_directory_group_types' ); ?>



					<li id="groups-order-select" class="last filter">



						<label for="groups-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>

						<select id="groups-order-by">

							<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>

							<option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>

							<option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>

							<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>



							<?php do_action( 'bp_groups_directory_order_options' ); ?>



						</select>

					</li>

				</ul>

			</div>



			<div id="groups-dir-list" class="groups dir-list">



				<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>



			</div><!-- #groups-dir-list -->



			<?php do_action( 'bp_directory_groups_content' ); ?>



			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>



			<?php do_action( 'bp_after_directory_groups_content' ); ?>



		</form><!-- #groups-directory-form -->



		<?php do_action( 'bp_after_directory_groups' ); ?>



		</div><!-- .padder -->

	</div><!-- #content -->



	<?php do_action( 'bp_after_directory_groups_page' ); ?>



<?php get_footer( 'buddypress' ); ?>

